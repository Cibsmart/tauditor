<?php

namespace App\Exports;

use App\Models\AuditSubMdaSchedule;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AutoPayScheduleExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    use Exportable;

    protected $sub_mda;

    /**
     * @inheritDoc
     */
    public function query()
    {
        return $this->sub_mda->autopaySchedules();
    }

    public function forSubMda(AuditSubMdaSchedule $sub_mda)
    {
        $this->sub_mda = $sub_mda;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function map($row) : array
    {
        return [
            $row->payment_reference,
            $row->beneficiary_code,
            $row->beneficiary_name,
            $row->account_number,
            $row->account_type,
            $row->cbn_code,
            $row->is_cash_card,
            $row->narration,
            $row->amount,
            $row->email,
            $row->currency,
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
