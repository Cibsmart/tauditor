<?php

namespace App\Imports;

use App\Exceptions\WrongScheduleException;
use App\Models\Bank;
use App\Models\OtherAuditPayrollCategory;
use function collect;
use Exception;
use Illuminate\Support\Str;
use function in_array;
use function is_null;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use function str_pad;
use const STR_PAD_LEFT;
use function throw_if;

class OtherScheduleImport implements OnEachRow
{
    use Importable;

    protected $domain;

    protected $heading;

    protected $headers = [];

    public OtherAuditPayrollCategory $payroll_category;

    public string $file_path;

    public function __construct(OtherAuditPayrollCategory $payroll_category, $file_path)
    {
        $this->payroll_category = $payroll_category;
        $this->file_path = $file_path;
    }

    public function onRow(Row $row)
    {
        $row_number = $row->getIndex();
        $columns = $row->toArray();

        if ($row_number === 1) {
            $this->domain = $this->payroll_category->auditPayroll->domain;

            $this->heading = collect($columns)->map(fn ($value) => Str::slug($value, '_'))->toArray();

            $this->setHeaders();

            return null;
        }

        //Combines each beneficiary record with the heading for identification
        $beneficiary = array_combine($this->heading, $columns);

        if (
            ! isset($beneficiary[$this->headers['beneficiary_name']]) ||
            ! isset($beneficiary[$this->headers['account_number']]) ||
            ! isset($beneficiary[$this->headers['amount']])
        ) {
            return null;
        }

        $this->createOtherPaySchedule($beneficiary);
    }

    protected function setHeaders()
    {
        $items = [
            'sn'               => ['sn', 's/no', 'sno'],
            'beneficiary_name' => ['name', 'names', 'beneficiaries', 'beneficiary', 'employee'],
            'narration'        => ['narration', 'description'],
            'amount'           => ['amount', 'amounts'],
            'account_number'   => ['account_number', 'account_num', 'account_no', 'account'],
            'bank_name'        => ['bank', 'bank_name'],
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

    protected function createOtherPaySchedule($beneficiary)
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

        if (is_null($bankable)) {
            throw_if(
                true,
                WrongScheduleException::class,
                "Bank Named: {$beneficiary[$this->headers['bank_name']]} for {$beneficiary[$this->headers['beneficiary_name']]} Does Not Exist"
            );
        }

        $bankable_type = $bankable->bankableType();

        $account_number = $bankable_type === 'commercial'
            ? $this->pad($beneficiary[$this->headers['account_number']], 10)
            : $beneficiary[$this->headers['account_number']];

        $bank_code = $bankable->bankCode();

        $attributes = [
            'serial_number'    => $beneficiary[$this->headers['sn']],
            'beneficiary_name' => $beneficiary[$this->headers['beneficiary_name']],
            'narration'        => $beneficiary[$this->headers['narration']],
            'amount'           => $beneficiary[$this->headers['amount']],
            'account_number'   => $account_number,
            'bank_name'        => $beneficiary[$this->headers['bank_name']],
            'bank_code'        => $this->pad($bank_code, 3),
            'bankable_type'    => $bankable_type,
            'bankable_id'      => $bankable->id,
        ];

        $schedule = null;

        try {
            $schedule = $this->payroll_category->auditOtherPaySchedules()->create($attributes);
        } catch (Exception $e) {
            throw_if(
                true,
                WrongScheduleException::class,
                $e->getMessage()
            );
        }

        return $schedule;
    }

    protected function getBankableType($bank_name)
    {
        $bank_name = Str::of(trim($bank_name))->upper()->trim();

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
            'ECOBANK OF NIGERIA PLC'               => 'ECOBANK NIGERIA PLC',
            'NDIORAH MICRO FINANCE BANK'           => 'NDIORA MICRO FINANCE BANK',
            'UKWALA MICROFINANCE BANK LTD'         => 'UKWALA MICRO FINANCE BANK LTD',
        ];

        return $exceptions[$bank_name] ?? $bank_name;
    }

    protected function pad($string, $padding)
    {
        return str_pad($string, $padding, '0', STR_PAD_LEFT);
    }
}
