<?php

namespace App\Jobs;

use App\Exports\MfbGroupScheduleExport;
use App\Exports\MfbScheduleExport;
use App\Models\AuditPayrollCategory;
use App\Models\AuditSubMdaSchedule;
use App\Models\BeneficiaryType;
use App\Models\MicroFinanceBank;
use App\Models\MicrofinanceBankSchedule;
use App\Models\ScheduleZip;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use ZipArchive;

class BuildMfbScheduleZip implements ShouldQueue
{
    use Queueable;

    public int $timeout = 180;

    public function __construct(public int $categoryId) {}

    // Bound retries on wall-clock instead of attempt count so a worker
    // restart or release-on-missing-row doesn't burn the budget; must
    // be < redis retry_after (210s) to avoid concurrent re-issue.
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(15);
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $category = AuditPayrollCategory::find($this->categoryId);

        // Under a burst dispatch the row can be momentarily invisible to this
        // worker; release and retry rather than failing. A row that is truly
        // gone still surfaces once retryUntil() lapses.
        if ($category === null) {
            $this->release(5);

            return;
        }

        ScheduleZip::updateOrCreate(
            [
                'audit_payroll_category_id' => $this->categoryId,
                'type' => ScheduleZip::TYPE_MFB,
            ],
            [
                'status' => ScheduleZip::STATUS_BUILDING,
                'failed_at' => null,
                'failure_reason' => null,
            ],
        );

        $finalPath = ScheduleZip::pathFor($this->categoryId, ScheduleZip::TYPE_MFB);

        // Unique per attempt so a concurrent rebuild of the same category
        // can't write to and corrupt this worker's in-progress archive
        // before the atomic rename below.
        $tmpPath = $finalPath.'.'.Str::random(8).'.building';

        @mkdir(dirname($finalPath), 0755, true);

        $zip = new ZipArchive;

        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $exception = new RuntimeException("Cannot open MFB zip archive at {$tmpPath}");
            $this->recordFailure($exception);
            throw $exception;
        }

        try {
            $category->domain()->group
                ? $this->addGroupFiles($zip, $category)
                : $this->addFiles($zip, $category);

            $zip->close();

            if (file_exists($finalPath)) {
                @unlink($finalPath);
            }

            rename($tmpPath, $finalPath);

            ScheduleZip::where('audit_payroll_category_id', $this->categoryId)
                ->where('type', ScheduleZip::TYPE_MFB)
                ->update([
                    'status' => ScheduleZip::STATUS_READY,
                    'built_at' => now(),
                    'failed_at' => null,
                    'failure_reason' => null,
                ]);
        } catch (Throwable $e) {
            @$zip->close();
            @unlink($tmpPath);
            $this->recordFailure($e);
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->recordFailure($exception);
    }

    private function recordFailure(Throwable $e): void
    {
        ScheduleZip::updateOrCreate(
            [
                'audit_payroll_category_id' => $this->categoryId,
                'type' => ScheduleZip::TYPE_MFB,
            ],
            [
                'status' => ScheduleZip::STATUS_FAILED,
                'failed_at' => now(),
                'failure_reason' => Str::limit($e->getMessage(), 1000),
            ],
        );
    }

    private function addFiles(ZipArchive $zip, AuditPayrollCategory $category): void
    {
        $month_year = $category->monthYear();
        $directory = "{$category->payment_title} - MFB SCHEDULE - {$category->id}";

        $pairs = MicrofinanceBankSchedule::query()
            ->select('audit_sub_mda_schedule_id', 'micro_finance_bank_id')
            ->join('audit_sub_mda_schedules', 'microfinance_bank_schedules.audit_sub_mda_schedule_id', '=',
                'audit_sub_mda_schedules.id')
            ->join('audit_mda_schedules', 'audit_sub_mda_schedules.audit_mda_schedule_id', '=',
                'audit_mda_schedules.id')
            ->where('audit_mda_schedules.audit_payroll_category_id', $category->id)
            ->whereNotNull('audit_sub_mda_schedules.autopay_generated')
            ->groupBy('audit_sub_mda_schedule_id', 'micro_finance_bank_id')
            ->get();

        $subMdas = AuditSubMdaSchedule::whereIn('id', $pairs->pluck('audit_sub_mda_schedule_id')->unique())
            ->get()->keyBy('id');
        $mfbs = MicroFinanceBank::whereIn('id', $pairs->pluck('micro_finance_bank_id')->unique())
            ->get()->keyBy('id');

        foreach ($pairs as $pair) {
            $sub_mda = $subMdas[$pair->audit_sub_mda_schedule_id];
            $mfb = $mfbs[$pair->micro_finance_bank_id];

            $path = "$directory/{$mfb->name}/{$sub_mda->sub_mda_name} $month_year MFB SCHEDULE.xlsx";
            $xlsx = (new MfbScheduleExport)->forMfbs($mfb)->inSubMda($sub_mda)->raw('Xlsx');

            $zip->addFromString($path, $xlsx);
        }
    }

    private function addGroupFiles(ZipArchive $zip, AuditPayrollCategory $category): void
    {
        $month_year = $category->monthYear();
        $directory = "{$category->payment_title} - MFB SCHEDULE - {$category->id}";

        $rows = MicrofinanceBankSchedule::query()
            ->select('beneficiary_type_id', 'micro_finance_bank_id')
            ->join('audit_sub_mda_schedules', 'microfinance_bank_schedules.audit_sub_mda_schedule_id', '=',
                'audit_sub_mda_schedules.id')
            ->join('audit_mda_schedules', 'audit_sub_mda_schedules.audit_mda_schedule_id', '=',
                'audit_mda_schedules.id')
            ->join('mdas', 'audit_mda_schedules.mda_id', '=', 'mdas.id')
            ->where('audit_mda_schedules.audit_payroll_category_id', $category->id)
            ->whereNotNull('audit_sub_mda_schedules.autopay_generated')
            ->groupBy('beneficiary_type_id', 'micro_finance_bank_id')
            ->get();

        $types = BeneficiaryType::whereIn('id', $rows->pluck('beneficiary_type_id')->unique())
            ->get()->keyBy('id');
        $mfbs = MicroFinanceBank::whereIn('id', $rows->pluck('micro_finance_bank_id')->unique())
            ->get()->keyBy('id');

        foreach ($rows as $row) {
            $type = $types[$row->beneficiary_type_id];
            $mfb = $mfbs[$row->micro_finance_bank_id];

            $path = "$directory/{$mfb->name}/{$type->name} $month_year MFB SCHEDULE.xlsx";
            $xlsx = (new MfbGroupScheduleExport)->forMfbs($mfb)->inBeneficiaryType($category, $type)->raw('Xlsx');

            $zip->addFromString($path, $xlsx);
        }
    }
}
