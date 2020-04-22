<?php

namespace App\Exports;

use App\AutopaySchedule;
use Maatwebsite\Excel\Excel;
use App\AuditSubMdaSchedule;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

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
