<?php

namespace App\Imports;

use App\Bank;
use Carbon\Carbon;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use App\AuditSubMdaSchedule;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use App\Exceptions\WrongScheduleException;
use function collect;
use function throw_if;
use function array_combine;

class PensionPayScheduleImport implements OnEachRow
{
    use Importable;

    protected $mda;
    protected $month;
    protected $year;
    protected $domain;
    protected $heading;
    protected $department;

    public AuditSubMdaSchedule $audit_sub_mda_schedule;
    public string $file_path;

    public function __construct(AuditSubMdaSchedule $audit_sub_mda_schedule, $file_path)
    {
        $this->audit_sub_mda_schedule = $audit_sub_mda_schedule;
        $this->file_path = $file_path;
    }


    public function onRow(Row $row)
    {
        $row_number = $row->getIndex();
        $columns = $row->toArray();

        if ($row_number === 1) {
            $this->processRowOne($columns[0]);
            return null;
        }

        if ($row_number === 2) {
            $this->domain = $this->audit_sub_mda_schedule->domain();
            return null;
        }

        if ($row_number === 3) {
            $this->heading = collect($columns)->map(fn ($value) => Str::slug($value, '_'))->toArray();
            return null;
        }

        throw_if($this->notMatching(), WrongScheduleException::class);

        //Combines each beneficiary record with the heading for identification
        $beneficiary = array_combine($this->heading, $columns);

        if (! isset($beneficiary['employee_id'])) {
            return null;
        }

        $this->createAuditPaySchedule($beneficiary);
    }

    /**
     * Extracts the MDA Name, Payment Month & Year from the First Row
     * @param  string  $row_one
     */
    private function processRowOne(string $row_one)
    {
        $mda_dept_month_year = Str::of($row_one)->after('ZONE: ')->upper()->replace(' PENSION', '')->explode(', ');

        $this->mda = $mda_dept_month_year[0];
        $this->department = $mda_dept_month_year[0];

        $month_year = Str::of($mda_dept_month_year[1])->explode(' ');

        $this->month = $month_year[0];
        $this->year = $month_year[1];

        return;
    }


    /**
     * Extract Beneficiary's Info, Allowances, Deductions and Save in Audit Pay Schedule Table
     * @param $beneficiary
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function createAuditPaySchedule($beneficiary)
    {
        $bankable = $this->getBankableType($beneficiary['bank_name']);

        $month = Carbon::parse("25 $this->month $this->year");

        $attributes = [
            'verification_number' => $beneficiary['employee_id'],
            'beneficiary_name'    => $beneficiary['employee_name'],
            'designation'         => 'PENSIONER',
            'basic_pay'           => $beneficiary['basic_pay'],
            'bank_name'           => $beneficiary['bank_name'],
            'account_number'      => $beneficiary['account_number'],
            'bank_code'           => $beneficiary['bank_code'],
            'total_allowance'     => 0,
            'gross_pay'           => $beneficiary['basic_pay'],
            'total_deduction'     => $beneficiary['total_deduction'],
            'net_pay'             => $beneficiary['net_pay'],
            'allowances'          => [],
            'deductions'          => [],
            'month'               => $month,
            'bankable_type'       => $bankable->bankableType(),
            'bankable_id'         => $bankable->id,
            'pension'             => 1,
        ];

        return $this->audit_sub_mda_schedule->auditPaySchedules()->create($attributes);
    }

    private function notMatching() : bool
    {
        return Str::upper($this->month) != Str::upper($this->audit_sub_mda_schedule->month())
            || Str::upper($this->year) != Str::upper($this->audit_sub_mda_schedule->year())
            || Str::upper($this->department) != Str::upper($this->audit_sub_mda_schedule->sub_mda_name);
    }

    private function getBankableType($bank_name)
    {
        Str::of($bank_name)->upper()->trim();

        $commercial = Bank::where('name', $bank_name)->first();

        if ($commercial) {
            return $commercial;
        }

        $bank_name = $this->checkMfbExists($bank_name);

        return $this->domain->microFinanceBanks->where('name', $bank_name)->first();
    }

    private function checkMfbExists($bank_name)
    {
        $exceptions = [
            'NDIOLU MICRO FINANCE BANK'           => 'NDIOLU MICRO FINANCE BANK, AWKA',
            'EZEBO MICRO FINANCE BANK LTD'        => 'EZEBO MICRO FINANCE BANK, UMUDIOKA',
            'TOPCLASS MICRO FINANCE BANK LIMITED' => 'TOP CLASS MICRO FINANCE BANK, ONITSHA',
        ];

        return $exceptions[$bank_name] ?? $bank_name;
    }
}
