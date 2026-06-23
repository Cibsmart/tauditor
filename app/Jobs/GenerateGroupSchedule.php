<?php

namespace App\Jobs;

use App\Actions\GenerateGroupAutopayScheduleAction;
use App\Models\AuditPayrollCategory;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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

        $action->execute($domain, $category, $beneficiaryType);
    }
}
