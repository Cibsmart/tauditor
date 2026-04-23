<?php

namespace App\Exports;

use App\Models\MicroFinanceBank;
use App\Models\OtherAuditPayrollCategory;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MfbOtherScheduleExport implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping
{
    use Exportable;

    protected OtherAuditPayrollCategory $category;

    protected MicroFinanceBank $mfb;

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        return $this->category->microfinanceSchedules()->where('micro_finance_bank_id', $this->mfb->id);
    }

    public function inCategory(OtherAuditPayrollCategory $category)
    {
        $this->category = $category;

        return $this;
    }

    public function forMfbs(MicroFinanceBank $mfb)
    {
        $this->mfb = $mfb;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function map($schedule): array
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
     * {@inheritDoc}
     */
    public function headings(): array
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
     * {@inheritDoc}
     */
    public function columnFormats(): array
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
