<?php

namespace App\Exports;

use App\Models\AuditPayrollCategory;
use App\Models\AutopaySchedule;
use App\Models\BeneficiaryType;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AutoPayGroupScheduleExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    use Exportable;

    protected AuditPayrollCategory $category;
    protected BeneficiaryType $beneficiaryType;

    /**
     * @inheritDoc
     */
    public function query()
    {
        return AutopaySchedule::query()
            ->select(
                'autopay_schedules.id',
                'payment_reference',
                'beneficiary_code',
                'beneficiary_name',
                'account_number',
                'account_type',
                'cbn_code',
                'is_cash_card',
                'narration',
                'amount',
                'email',
                'currency'
            )
            ->join('audit_sub_mda_schedules', 'autopay_schedules.audit_sub_mda_schedule_id', '=', 'audit_sub_mda_schedules.id')
            ->join('audit_mda_schedules', 'audit_sub_mda_schedules.audit_mda_schedule_id', '=', 'audit_mda_schedules.id')
            ->where('audit_payroll_category_id', $this->category->id)
            ->where('beneficiary_type_id', $this->beneficiaryType->id);
    }

    public function forBeneficiaryType(AuditPayrollCategory $category, BeneficiaryType $beneficiaryType)
    {
        $this->category = $category;
        $this->beneficiaryType = $beneficiaryType;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function map($schedule) : array
    {
        return [
            $schedule->payment_reference,
            $schedule->beneficiary_code,
            $schedule->beneficiary_name,
            $schedule->account_number,
            $schedule->account_type,
            $schedule->cbn_code,
            $schedule->is_cash_card,
            $schedule->narration,
            $schedule->amount,
            $schedule->email,
            $schedule->currency,
        ];
    }

    /**
     * @inheritDoc
     */
    public function headings() : array
    {
        return [
            'Payment Reference',
            'Beneficiary Code',
            'Beneficiary Name',
            'Account Number',
            'Account Type',
            'CBN Code',
            'Is CashCard',
            'Narration',
            'Amount',
            'Email Address',
            'Currency Code',
        ];
    }

    /**
     * @inheritDoc
     */
    public function columnFormats() : array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
