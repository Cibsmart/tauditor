<?php

namespace App\Imports;

use Exception;
use Carbon\Carbon;
use App\Models\Bank;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use App\Models\AuditSubMdaSchedule;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use App\Exceptions\WrongScheduleException;
use function dump;
use function str_pad;
use function collect;
use function throw_if;
use function in_array;
use const STR_PAD_LEFT;

class PayScheduleImport implements OnEachRow
{
    use Importable;

    protected $mda;
    protected $month;
    protected $year;
    protected $domain;
    protected $heading;
    protected $department;
    protected $headers = [];

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

        $app_date = Str::upper($this->audit_sub_mda_schedule->month().' '.$this->audit_sub_mda_schedule->year());
        $file_date = $this->month.' '.$this->year;
        $message = "Trying to Upload Schedule for $file_date into $app_date";

        throw_if(
            $this->monthAndYearNotMatching(),
            WrongScheduleException::class,
            $message
        );

        $app_mda = Str::upper($this->audit_sub_mda_schedule->sub_mda_name);
        $file_mda = $this->department;
        $message = "Trying to Upload Schedule for $file_mda into $app_mda";

        throw_if(
            $this->mdaNotMatching(),
            WrongScheduleException::class,
            $message
        );

        if ($row_number === 2) {
            //set domain
            $this->domain = $this->audit_sub_mda_schedule->domain();
            return null;
        }

        if ($row_number === 3) {
            $this->heading = collect($columns)->map(fn ($value) => Str::slug($value, '_'))->toArray();

            $this->setHeaders();

            return null;
        }


        //Combines each beneficiary record with the heading for identification
        $beneficiary = array_combine($this->heading, $columns);

        if (
            ! isset($beneficiary[$this->headers['employee_id']]) ||
            ! isset($beneficiary[$this->headers['employee_name']])
        ) {
            return null;
        }

        $this->createAuditPaySchedule($beneficiary);
    }

    /**
     * Extracts the MDA Name, Payment Month & Year from the First Row
     * @param  string  $row_one
     */
    protected function processRowOne(string $row_one)
    {
        $mda_dept_month_year = Str::of($row_one)->contains('MDA/PARASTATAL')
            ? Str::of($row_one)->after('MDA/PARASTATAL: ')->upper()->explode(', ')
            : Str::of($row_one)->after('LGA/PARASTATAL: ')->upper()->explode(', ');

        if ($mda_dept_month_year->count() === 3) {
            $this->mda = $mda_dept_month_year[0];
            $this->department = $this->mdaNameCheck($mda_dept_month_year[0]);

            $month_year = Str::of($mda_dept_month_year[1])->explode(' ');

            $this->month = $month_year[0];
            $this->year = $month_year[1];

            return;
        }

        $this->mda = $mda_dept_month_year[0];
        $this->department = $mda_dept_month_year[1];

        $month_year = Str::of($mda_dept_month_year[2])->explode(' ');

        $this->month = $month_year[0];
        $this->year = $month_year[1];

        return;
    }

    /**
     * Extract Beneficiary's Info, Allowances, Deductions and Save in Audit Pay Schedule Table
     * @param $beneficiary
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createAuditPaySchedule($beneficiary)
    {
        $all = collect($beneficiary);

        $part_a = $all->takeUntil(fn (
            $item,
            $key
        ) => $key == $this->headers['bank_code']); //Gets all the beneficiary info part

        $other_part = $all->diffKeys($part_a)->except($this->headers['bank_code']); //Remove part_a from all
        $allowances = $other_part->takeUntil(fn ($item, $key) => $key == $this->headers['total_allowance'])->filter();

        $deductions = $other_part->diffKeys($allowances)
                                 ->except(
                                     $this->headers['total_allowance'],
                                     $this->headers['gross_pay'],
                                     $this->headers['total_dues'],
                                     $this->headers['total_deduction'],
                                     $this->headers['net_pay']
                                 )->filter();

        try {
            $bankable = $this->getBankableType($beneficiary[$this->headers['bank_name']]);
        } catch (Exception $e) {
            throw_if(
                true,
                WrongScheduleException::class,
                'Bank Name: '.$beneficiary[$this->headers['bank_name']].' '.$e->getMessage()
            );
        }

        $month = Carbon::parse("25 $this->month $this->year");

        $designation = $beneficiary[$this->headers['designation']] == ''
            ? 'None'
            : $beneficiary[$this->headers['designation']];

        if (is_null($bankable)) {
            throw_if(
                true,
                WrongScheduleException::class,
                "Bank Named: {$beneficiary[$this->headers['bank_name']]} for {$beneficiary[$this->headers['employee_id']]} Does Not Exist"
            );
        }

        $bankable_type = $bankable->bankableType();

        $account_number = $bankable_type === 'commercial'
            ? $this->pad($beneficiary[$this->headers['account_number']], 10)
            : $beneficiary[$this->headers['account_number']];

        $attributes = [
            'verification_number' => $beneficiary[$this->headers['employee_id']],
            'beneficiary_name'    => $beneficiary[$this->headers['employee_name']],
            'beneficiary_cadre'   => $beneficiary[$this->headers['employee_grade']],
            'designation'         => $designation,
            'basic_pay'           => $beneficiary[$this->headers['basic_salary']],
            'bank_name'           => $beneficiary[$this->headers['bank_name']],
            'account_number'      => $account_number,
            'bank_code'           => $this->pad($beneficiary[$this->headers['bank_code']], 3),
            'total_allowance'     => $beneficiary[$this->headers['total_allowance']],
            'gross_pay'           => $beneficiary[$this->headers['gross_pay']],
            'total_deduction'     => $beneficiary[$this->headers['total_deduction']] + $beneficiary[$this->headers['total_dues']],
            'net_pay'             => $beneficiary[$this->headers['net_pay']],
            'allowances'          => $allowances,
            'deductions'          => $deductions,
            'month'               => $month,
            'bankable_type'       => $bankable_type,
            'bankable_id'         => $bankable->id,
        ];

        $schedule = null;

        try {
            $schedule = $this->audit_sub_mda_schedule->auditPaySchedules()->create($attributes);
        } catch (Exception $e) {
            throw_if(
                true,
                WrongScheduleException::class,
                $e->getMessage()
            );
        }

        return $schedule;
    }

    protected function monthAndYearNotMatching() : bool
    {
        return Str::upper($this->month) != Str::upper($this->audit_sub_mda_schedule->month())
            || Str::upper($this->year) != Str::upper($this->audit_sub_mda_schedule->year());
    }

    protected function mdaNotMatching() : bool
    {
        return Str::upper($this->department) != Str::upper($this->audit_sub_mda_schedule->sub_mda_name);
    }

    protected function getBankableType($bank_name)
    {
        Str::of($bank_name)->upper()->trim();

        $bank_name = $this->checkBankExceptions($bank_name);

        $commercial = Bank::where('name', $bank_name)->first();

        if ($commercial) {
            return $commercial;
        }

        return $this->domain->microFinanceBanks->where('name', $bank_name)->first();
    }

    protected function checkBankExceptions($bank_name)
    {
        $bank_name = Str::upper($bank_name);

        $exceptions = [
            'FIDELITY'                             => 'FIDELITY BANK PLC',
            'POLARIS BANK OF NIGERIA PLC'          => 'SKYE BANK PLC',
            'POLORIS BANK OF NIGERIA PLC'          => 'SKYE BANK PLC',
            'FIRST BANK PLC.'                      => 'FIRST BANK OF NIGERIA PLC',
            'UNITED BANK FOR AFRICA'               => 'UNITED BANK FOR AFRICA PLC',
            'NDIOLU MICRO FINANCE BANK'            => 'NDIOLU MICRO FINANCE BANK, AWKA',
            'EZEBO MICRO FINANCE BANK LTD'         => 'EZEBO MICRO FINANCE BANK, UMUDIOKA',
            'TOPCLASS MICRO FINANCE BANK LIMITED'  => 'TOP CLASS MICRO FINANCE BANK, ONITSHA',
            'NDIOLU MICROFINANCE BANK'             => 'NDIOLU MICRO FINANCE BANK, AWKA',
            'OLUCHUKWU MICRO FINANCE BANK,ONITSHA' => 'OLUCHUKWU MICRO FINANCE BANK, ONITSHA',
            'UNITED BANK OF AFRICA'                => 'UNITED BANK FOR AFRICA PLC',
            'HERITAGE BANK'                        => 'HERITAGE BANK LIMITED',
            'UNION BANK'                           => 'UNION BANK OF NIGERIA PLC',
            'FIRST BANK'                           => 'FIRST BANK OF NIGERIA PLC',
            'UNITY BANK'                           => 'UNITY BANK PLC',
            'POLARIS BANK PLC'                     => 'SKYE BANK PLC',
            'MAYFRESH SAVINGS ANG LOAN'            => 'MAYFRESH SAVINGS AND LOAN',
            'UNION BANK NIGERIA PLC'               => 'UNION BANK OF NIGERIA PLC',
            'ZENITH BANK'                          => 'ZENITH BANK PLC',
            'EZNITH BANK PLC'                      => 'ZENITH BANK PLC',
            'FIDELITY BBANK PLC'                   => 'FIDELITY BANK PLC',
            'STANBIC IBTC BANK PLC'                => 'STANBIC-IBTC BANK PLC',
            'ECOBANK'                              => 'ECOBANK NIGERIA PLC',
        ];

        return $exceptions[$bank_name] ?? $bank_name;
    }

    protected function mdaNameCheck($mda)
    {
        $exceptions = [
            'POLITICAL APPOINTEES GOVT HOUSE' => 'POLITICAL APPOINTEES GOVERNMENT HOUSE',
        ];

        return $exceptions[$mda] ?? $mda;
    }

    protected function pad($string, $padding)
    {
        return str_pad($string, $padding, '0', STR_PAD_LEFT);
    }

    protected function setHeaders()
    {
        $items = [
            'employee_id'     => ['id', 'employee_id'],
            'employee_name'   => ['name', 'employee_name'],
            'employee_grade'  => ['grade', 'employee_grade'],
            'designation'     => ['designation', 'employee_designation'],
            'basic_salary'    => ['bs', 'basic_salary'],
            'bank_name'       => ['bank', 'bank_name'],
            'account_number'  => ['acct', 'account_number'],
            'bank_code'       => ['code', 'bank_code'],
            'total_allowance' => ['total_allw', 'total_allowance'],
            'gross_pay'       => ['gross', 'grosspay', 'gross_pay'],
            'total_dues'      => ['total_dues'],
            'total_deduction' => ['total_ded', 'total_deduction'],
            'net_pay'         => ['net', 'netpay', 'net_pay'],
        ];

        foreach ($items as $key => $value) {
            $this->headers[$key] = $this->heading[$this->getKeyFor($value)];
        }
    }

    protected function getKeyFor($array)
    {
        $heading = collect($this->heading);

        return $heading->search(fn ($item, $key) => in_array($item, $array));
    }
}
