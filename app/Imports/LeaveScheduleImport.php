<?php

namespace App\Imports;

use Exception;
use Carbon\Carbon;
use App\Models\Bank;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\AuditSubMdaSchedule;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use App\Exceptions\WrongScheduleException;
use Maatwebsite\Excel\Concerns\ToCollection;
use function count;
use function collect;
use function str_pad;
use function throw_if;
use function in_array;
use function array_combine;
use const STR_PAD_LEFT;

class LeaveScheduleImport implements OnEachRow
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
        $this->domain = Auth::user()->domain;
    }


    public function onRow(Row $row)
    {
        $row_number = $row->getIndex();
        $columns = $row->toArray();

        if ($row_number === 1) {
            return null;
        }

        if ($row_number === 2) {
            $this->processRowTwo($columns[0]);
            return null;
        }

        if ($row_number === 3) {
            return null;
        }

        if ($row_number === 4) {
            $this->heading = collect($columns)->map(fn ($value) => Str::slug($value, '_'))->toArray();

            $this->setHeaders();

            return null;
        }

        $app_date = $this->audit_sub_mda_schedule->year();
        $file_date = $this->year;
        $message = "Trying to Upload Schedule for $file_date into $app_date";

        throw_if(
            $this->yearNotMatching(),
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

        //Combines each beneficiary record with the heading for identification
        $beneficiary = array_combine($this->heading, $columns);

        if (! isset($beneficiary['employee_id']) || empty($beneficiary['employee_id'])) {
            return null;
        }

        $this->createAuditPaySchedule($beneficiary);
    }

    /**
     * Extracts the MDA Name, Payment Month & Year from the First Row
     * @param  string  $row_two
     */
    protected function processRowTwo(string $row_two)
    {
        $row = Str::of($row_two)->after('MDA/PARASTATAL: ')->upper()->explode(', ');

        $count = count($row);

        $this->mda = $row[0];

        $title = $count === 2 ? $row[1] : $row[2];

        $this->department = $count === 2 ? $this->mdaNameCheck($row[0]) : $this->mdaNameCheck($row[1]);

        $month_year = Str::of($title)->explode(' ');

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
        try {
            $bankable = $this->getBankableType($beneficiary[$this->headers['bank_name']]);
        } catch (Exception $e) {
            throw_if(
                true,
                WrongScheduleException::class,
                'Bank Name: ' . $beneficiary[$this->headers['bank_name']] . ' ' . $e->getMessage()
            );
        }

        $month = Carbon::parse("25 $this->month $this->year")->endOfMonth();

        if ($bankable === null) {
            throw_if(
                true,
                WrongScheduleException::class,
                'Bank Named: '.$beneficiary[$this->headers['bank_name']].' Does Not Exist'
            );
        }
        $bankable_type = $bankable->bankableType();

        $account_number = $bankable_type === 'commercial'
            ? $this->pad($beneficiary[$this->headers['account_number']], 10)
            : $beneficiary[$this->headers['account_number']];

        $attributes = [
            'verification_number' => $beneficiary[$this->headers['employee_id']],
            'beneficiary_name'    => Str::upper($beneficiary[$this->headers['employee_name']]),
            'beneficiary_cadre'   => $beneficiary[$this->headers['employee_grade']],
            'designation'         => '',
            'basic_pay'           => 0,
            'bank_name'           => $beneficiary[$this->headers['bank_name']],
            'account_number'      => $account_number,
            'bank_code'           => $this->pad($this->getBankCode($bankable), 3),
            'total_allowance'     => 0,
            'gross_pay'           => 0,
            'total_deduction'     => 0,
            'net_pay'             => $beneficiary[$this->headers['net_pay']],
            'allowances'          => [],
            'deductions'          => [],
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

    protected function yearNotMatching()
    {
        return $this->year != $this->audit_sub_mda_schedule->year();
    }

    protected function mdaNotMatching()
    {
        return Str::upper($this->department) != Str::upper($this->audit_sub_mda_schedule->sub_mda_name);
    }

    protected function getBankableType($bank_name)
    {
        Str::of($bank_name)->upper()->trim();

        $bank_name = $this->checkBankExceptions($bank_name);

        $commercial = Bank::where('name', $bank_name)->first();

        return $commercial ?? $this->domain->microFinanceBanks->where('name', $bank_name)->first();
    }

    protected function getBankCode($bankable)
    {
        return $bankable->code ?? $bankable->bank->code;
    }

    protected function checkBankExceptions($bank_name)
    {
        $bank_name = Str::upper(Str::of($bank_name)->replace('?', ''));

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
            'ACCESS BANK'                          => 'ACCESS BANK PLC',
            'STANBIC IBTC'                         => 'STANBIC-IBTC BANK PLC',
            'FIRST CITY MONOMENT BANK PLC'         => 'FIRST CITY MONUMENT BANK PLC',
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

    public function pad($string, $padding)
    {
        return str_pad($string, $padding, '0', STR_PAD_LEFT);
    }

    protected function setHeaders()
    {
        $items = [
            'employee_id'     => ['id', 'employee_id'],
            'employee_name'   => ['name', 'employee_name'],
            'employee_grade'  => ['grade', 'employee_grade'],
            'bank_name'       => ['bank', 'bank_name'],
            'account_number'  => ['acct', 'account_number', 'account_no', 'bank_account'],
            'bank_code'       => ['code', 'bank_code'],
            'net_pay'         => ['net', 'netpay', 'net_pay', 'leave_allowance'],
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
