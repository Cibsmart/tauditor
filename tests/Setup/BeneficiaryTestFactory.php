<?php

namespace Tests\Setup;

use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Beneficiary;
use App\Models\MicroFinanceBank;
use Facades\BeneficiaryFactory;
use Faker\Generator;

class BeneficiaryTestFactory
{
    private Generator $faker;

    private $bank = null;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * @return $this
     */
    public function withMfb(?MicroFinanceBank $micro_finance_bank = null)
    {
        $this->bank = $micro_finance_bank ?? MicroFinanceBank::factory()->create();

        return $this;
    }

    public function withBank(?Bank $bank = null)
    {
        $this->bank = $bank ?? Bank::factory()->create();

        return $this;
    }

    public function create($override = [])
    {
        BeneficiaryFactory::clearResolvedInstance('BeneficiaryTestFactory');

        $beneficiary = Beneficiary::factory()->create($override);

        if ($this->bank) {
            $this->bank->beneficiaries()->save(BankDetail::factory()->make([
                'beneficiary_id' => $beneficiary->id,
            ]));
        }

        return $beneficiary;
    }
}
