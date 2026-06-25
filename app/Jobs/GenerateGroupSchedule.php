<?php

namespace App\Jobs;

use App\Actions\GenerateGroupAutopayScheduleAction;
use App\Models\AuditPayrollCategory;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateGroupSchedule implements ShouldQueue
{
    use Queueable;

    public int $timeout = 180;

    public function __construct(
        public string $domainId,
        public int $categoryId,
        public string $beneficiaryTypeId,
    ) {}

    // Bound retries on wall-clock instead of attempt count so a worker
    // restart or release-on-missing-row doesn't burn the budget; must
    // be < redis retry_after (210s) to avoid concurrent re-issue.
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(15);
    }

    public function handle(GenerateGroupAutopayScheduleAction $action): void
    {
        $domain = Domain::find($this->domainId);
        $category = AuditPayrollCategory::find($this->categoryId);
        $beneficiaryType = BeneficiaryType::find($this->beneficiaryTypeId);

        // Under a burst dispatch a row can be momentarily invisible to this
        // worker; release and retry rather than failing. Rows that are truly
        // gone still surface once retryUntil() lapses.
        if ($domain === null || $category === null || $beneficiaryType === null) {
            $this->release(5);

            return;
        }

        // A retry may land after a prior attempt already generated this
        // beneficiary type; skip to avoid creating duplicate schedule rows.
        if ($category->generatedBeneficiaryTypes()->contains($this->beneficiaryTypeId)) {
            return;
        }

        // Serialize only concurrent re-issues of this same (category,
        // beneficiary type) pair so a retry can't generate duplicate rows,
        // while different beneficiary types of the category still run in
        // parallel. This replaces the old category-wide row lock, which
        // serialized every beneficiary type and timed out under MySQL's
        // innodb_lock_wait_timeout. The TTL outlives the job timeout (180s)
        // so the lock self-heals if a worker dies mid-run.
        $lock = Cache::lock("group-autopay:{$this->categoryId}:{$this->beneficiaryTypeId}", 200);

        if (! $lock->get()) {
            $this->release(5);

            return;
        }

        try {
            $action->execute($domain, $category, $beneficiaryType);
        } finally {
            $lock->release();
        }
    }

    /**
     * A terminal failure (timeout, exhausted retries, or thrown exception) would
     * otherwise leave the category stuck on the 'running' status its controller
     * set at dispatch. Mark it 'failed' so the UI reflects that generation
     * errored, and record the reason for triage.
     */
    public function failed(?Throwable $exception): void
    {
        $category = AuditPayrollCategory::find($this->categoryId);

        if ($category !== null && $category->autopay_status === 'running') {
            $category->setAutopayStatus('failed');
        }

        Log::error('Autopay group-schedule generation failed.', [
            'category_id' => $this->categoryId,
            'beneficiary_type_id' => $this->beneficiaryTypeId,
            'reason' => $exception?->getMessage(),
        ]);
    }
}
