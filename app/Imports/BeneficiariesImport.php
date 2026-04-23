<?php

namespace App\Imports;

use const STR_PAD_LEFT;

use App\Exceptions\WrongScheduleException;
use App\Models\Bank;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

use function array_combine;
use function collect;
use function in_array;
use function is_null;
use function str_pad;
use function throw_if;

class BeneficiariesImport implements OnEachRow
{
    use Importable;

    protected $domain;

    protected $heading;

    public function onRow(Row $row)
    {
        $row_number = $row->getIndex();
        $columns = $row->toArray();

        if ($row_number === 1) {
            $this->heading = collect($columns)->map(fn ($value) => Str::slug($value, '_'))->toArray();

            $this->domain = Auth::user()->domain;

            return null;
        }

        $beneficiary = array_combine($this->heading, $columns);

        if (
            ! isset($beneficiary['surname']) ||
            ! isset($beneficiary['first_name']) ||
            ! isset($beneficiary['staff_id'])
        ) {
            return null;
        }

        $this->createBeneficiary($beneficiary);
    }

    protected function createBeneficiary($beneficiary)
    {
        try {
            $bankable = $this->getBankableType($beneficiary['bank']);
        } catch (Exception $e) {
            throw_if(
                true,
                WrongScheduleException::class,
                'Bank Name: '.$beneficiary['bank_name'].' '.$e->getMessage()
            );
        }

        if (is_null($bankable)) {
            throw_if(
                true,
                WrongScheduleException::class,
                "Bank Named: {$beneficiary['bank_name']} for {$beneficiary['staff_id']} Does Not Exist"
            );
        }

        $bankable_type = $bankable->bankableType();

        $account_number = $bankable_type === 'commercial'
            ? $this->pad($beneficiary['account_number'], 10)
            : $beneficiary['account_number'];

        $dob = Carbon::create($beneficiary['year'], $beneficiary['month'], $beneficiary['day'], 0, 0, 0);

        $attributes = [
            'verification_number' => Str::upper($beneficiary['staff_id']),
            'last_name' => Str::upper($beneficiary['surname']),
            'first_name' => Str::upper($beneficiary['first_name']),
            'middle_name' => Str::upper($beneficiary['middle_name']),
            'date_of_birth' => $dob,
            'gender_id' => $beneficiary['gender'],
            'marital_status_id' => $beneficiary['marital_status'],
            'nationality_id' => $beneficiary['nationality'],
            'state_id' => $beneficiary['state'],
            'local_government_id' => $beneficiary['lga'],
            'domain_id' => $this->domain->id,
            'beneficiary_type_id' => $beneficiary['beneficiary_type'],
        ];

        $beneficiary_new = null;

        try {
            $beneficiary_new = Beneficiary::create($attributes);
        } catch (Exception $e) {
            throw_if(
                true,
                WrongScheduleException::class,
                $e->getMessage()
            );
        }

        $beneficiary_new->bankDetail()->create([
            'account_name' => Str::upper($beneficiary['account_name']),
            'account_number' => $account_number,
            'bank_verification_number' => $beneficiary['bvn'],
            'bankable_type' => $bankable_type,
            'bankable_id' => $bankable->id,
        ]);

        $info = $beneficiary_new->info()->create([
            'staff_id' => $beneficiary['staff_id'],
            'staff_type' => $this->checkNull(Str::upper($beneficiary['staff_type'])),
            'grade' => $this->checkNull($beneficiary['grade']),
            'step' => $this->checkNull($beneficiary['step']),
            'ministry' => $this->checkNull(Str::upper($beneficiary['ministry'])),
            'department' => $this->checkNull(Str::upper($beneficiary['department'])),
            'image' => $this->checkNull($beneficiary['image']),
            'finger_print_1' => $this->checkNull($beneficiary['finger_1']),
            'finger_print_2' => $this->checkNull($beneficiary['finger_2']),
            'finger_print_3' => $this->checkNull($beneficiary['finger_3']),
        ]);

        return $beneficiary_new;
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
            'FIDELITY' => 'FIDELITY BANK PLC',
            'POLARIS BANK OF NIGERIA PLC' => 'SKYE BANK PLC',
            'POLORIS BANK OF NIGERIA PLC' => 'SKYE BANK PLC',
            'FIRST BANK PLC.' => 'FIRST BANK OF NIGERIA PLC',
            'UNITED BANK FOR AFRICA' => 'UNITED BANK FOR AFRICA PLC',
            'NDIOLU MICRO FINANCE BANK' => 'NDIOLU MICRO FINANCE BANK, AWKA',
            'EZEBO MICRO FINANCE BANK LTD' => 'EZEBO MICRO FINANCE BANK, UMUDIOKA',
            'TOPCLASS MICRO FINANCE BANK LIMITED' => 'TOP CLASS MICRO FINANCE BANK, ONITSHA',
            'NDIOLU MICROFINANCE BANK' => 'NDIOLU MICRO FINANCE BANK, AWKA',
            'OLUCHUKWU MICRO FINANCE BANK,ONITSHA' => 'OLUCHUKWU MICRO FINANCE BANK, ONITSHA',
            'UNITED BANK OF AFRICA' => 'UNITED BANK FOR AFRICA PLC',
            'HERITAGE BANK' => 'HERITAGE BANK LIMITED',
            'UNION BANK' => 'UNION BANK OF NIGERIA PLC',
            'FIRST BANK' => 'FIRST BANK OF NIGERIA PLC',
            'UNITY BANK' => 'UNITY BANK PLC',
            'POLARIS BANK PLC' => 'SKYE BANK PLC',
            'MAYFRESH SAVINGS ANG LOAN' => 'MAYFRESH SAVINGS AND LOAN',
            'UNION BANK NIGERIA PLC' => 'UNION BANK OF NIGERIA PLC',
            'ZENITH BANK' => 'ZENITH BANK PLC',
            'EZNITH BANK PLC' => 'ZENITH BANK PLC',
            'FIDELITY BBANK PLC' => 'FIDELITY BANK PLC',
            'STANBIC IBTC BANK PLC' => 'STANBIC-IBTC BANK PLC',
            'ECOBANK' => 'ECOBANK NIGERIA PLC',
            'STERLING BANK' => 'STERLING BANK PLC',
        ];

        return $exceptions[$bank_name] ?? $bank_name;
    }

    protected function pad($string, $padding)
    {
        return str_pad($string, $padding, '0', STR_PAD_LEFT);
    }

    protected function checkNull($value)
    {
        $checks = ['NULL', 'NA'];

        return in_array(Str::upper($value), $checks) ? null : $value;
    }
}
