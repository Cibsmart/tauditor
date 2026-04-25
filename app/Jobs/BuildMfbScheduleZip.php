<?php

namespace App\Jobs;

use App\Exports\MfbGroupScheduleExport;
use App\Exports\MfbScheduleExport;
use App\Models\AuditPayrollCategory;
use App\Models\AuditSubMdaSchedule;
use App\Models\BeneficiaryType;
use App\Models\MicroFinanceBank;
use App\Models\MicrofinanceBankSchedule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Throwable;
use ZipArchive;

class BuildMfbScheduleZip implements ShouldQueue
{
    use Queueable;

    public int $timeout = 0;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public AuditPayrollCategory $category) {}

    public static function statusKey(AuditPayrollCategory $category): string
    {
        return "mfb_zip:{$category->id}";
    }

    public static function zipPath(AuditPayrollCategory $category): string
    {
        return storage_path("app/mfb_exports/{$category->id}.zip");
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $finalPath = self::zipPath($this->category);
        $tmpPath = $finalPath.'.building';

        @mkdir(dirname($finalPath), 0755, true);

        $zip = new ZipArchive;

        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            Cache::forget(self::statusKey($this->category));
            throw new RuntimeException("Cannot open MFB zip archive at {$tmpPath}");
        }

        try {
            $this->category->domain()->group
                ? $this->addGroupFiles($zip)
                : $this->addFiles($zip);

            $zip->close();

            if (file_exists($finalPath)) {
                @unlink($finalPath);
            }

            rename($tmpPath, $finalPath);
        } catch (Throwable $e) {
            @$zip->close();
            @unlink($tmpPath);
            throw $e;
        } finally {
            Cache::forget(self::statusKey($this->category));
        }
    }

    public function failed(Throwable $exception): void
    {
        Cache::forget(self::statusKey($this->category));
    }

    private function addFiles(ZipArchive $zip): void
    {
        $category = $this->category;
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

    private function addGroupFiles(ZipArchive $zip): void
    {
        $category = $this->category;
        $month_year = $category->monthYear();
        $directory = "{$category->payment_title} - MFB SCHEDULE - {$category->id}";

        $rows = MicrofinanceBankSchedule::query()
            ->select('beneficiary_type_id', 'micro_finance_bank_id')
            ->join('audit_sub_mda_schedules', 'microfinance_bank_schedules.audit_sub_mda_schedule_id', '=',
                'audit_sub_mda_schedules.id')
            ->join('audit_mda_schedules', 'audit_sub_mda_schedules.audit_mda_schedule_id', '=',
                'audit_mda_schedules.id')
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
