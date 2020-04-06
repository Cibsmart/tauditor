<?php


namespace Tests\Setup;


use App\Bank;
use App\BankDetail;
use App\Beneficiary;
use Faker\Generator;
use App\SalaryDetail;
use App\MicroFinanceBank;
use App\StructuredSalary;
use App\PersonalizedSalary;
use function factory;

class BeneficiaryFactory
{
    private Generator $faker;
    private $bank = null;
    private $payable = null;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    public function withMfb(MicroFinanceBank $micro_finance_bank = null)
    {
        $this->bank = $micro_finance_bank ?? factory(MicroFinanceBank::class)->create() ;

        return $this;
    }

    public function withBank(Bank $bank = null)
    {
        $this->bank = $bank ?? factory(Bank::class)->create() ;

        return $this;
    }

    public function withPersonalizedSalary(PersonalizedSalary $personalized_salary = null)
    {
        $this->payable = $personalized_salary ?? factory(PersonalizedSalary::class)->create();

        return $this;
    }

    public function withStructuredSalary(StructuredSalary $structured_salary = null)
    {
        $this->payable = $structured_salary ?? factory(StructuredSalary::class)->create();

        return $this;
    }

    public function create($override = [])
    {
        $beneficiary = factory(Beneficiary::class)->create($override);

        if($this->bank) {
            $this->bank->beneficiaries()->save(factory(BankDetail::class)->make(['beneficiary_id' => $beneficiary->id]));
        }

        if($this->payable){
            $this->payable->salary()->save(factory(SalaryDetail::class)->make(['beneficiary_id' => $beneficiary->id]));
        }

        return $beneficiary;
    }
}
