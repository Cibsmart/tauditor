<?php

namespace Tests\Setup;

use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Beneficiary;
use App\Models\MicroFinanceBank;
use Facades\BeneficiaryFactory;
use Faker\Generator;

use function factory;

class BeneficiaryTestFactory
{
    private Generator $faker;

    private $bank = null;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * @param MicroFinanceBank|null $micro_finance_bank
     * @return $this
     */
    public function withMfb(MicroFinanceBank $micro_finance_bank = null)
    {
        $this->bank = $micro_finance_bank ?? factory(MicroFinanceBank::class)->create();

        return $this;
    }

    public function withBank(Bank $bank = null)
    {
        $this->bank = $bank ?? factory(Bank::class)->create();

        return $this;
    }

    public function create($override = [])
    {
        BeneficiaryFactory::clearResolvedInstance('BeneficiaryTestFactory');

        $beneficiary = factory(Beneficiary::class)->create($override);

        if ($this->bank) {
            $this->bank->beneficiaries()->save(factory(BankDetail::class)->make([
                'beneficiary_id' => $beneficiary->id,
            ]));
        }

        return $beneficiary;
    }
}
