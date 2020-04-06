<?php


namespace Tests\Setup;


use App\Bank;
use App\NextOfKin;
use App\MdaDetail;
use App\BankDetail;
use App\WorkDetail;
use App\Beneficiary;
use Faker\Generator;
use App\SalaryDetail;
use App\Qualification;
use App\MicroFinanceBank;
use App\StructuredSalary;
use App\PersonalizedSalary;
use function factory;

class BeneficiaryFactory
{
    private Generator $faker;
    private $bank = null;
    private $payable = null;
    private bool $mda = false;
    private bool $next_of_kin = false;
    private bool $work_detail = false;
    private int $qualification_count = 0;

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

    public function withNextOfKin()
    {
        $this->next_of_kin = true;

        return $this;
    }

    public function withMda()
    {
        $this->mda = true;

        return $this;
    }

    public function withQualifications($qualification_count = 1)
    {
        $this->qualification_count = $qualification_count;

        return $this;
    }

    public function withWorkDetail()
    {
        $this->work_detail = true;

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

        if($this->next_of_kin){
            $beneficiary->nextOfKin()->save(factory(NextOfKin::class)->make());
        }

        $beneficiary->qualifications()->saveMany(factory(Qualification::class, $this->qualification_count)->make());

        if($this->mda){
            $beneficiary->mdaDetail()->save(factory(MdaDetail::class)->make());
        }

        if($this->work_detail){
            $beneficiary->workDetail()->save(factory(WorkDetail::class)->make());
        }

        return $beneficiary;
    }
}
