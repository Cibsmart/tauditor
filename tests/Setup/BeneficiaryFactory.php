<?php


namespace Tests\Setup;


use App\Bank;
use App\NextOfKin;
use App\MdaDetail;
use App\CadreStep;
use App\Allowance;
use App\BankDetail;
use App\WorkDetail;
use App\FixedValue;
use App\Beneficiary;
use Faker\Generator;
use App\SalaryDetail;
use App\Qualification;
use App\AllowanceDetail;
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
    private ?float $monthly_basic = null;
    private ?int $allowance_count = null;

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
        if($personalized_salary){
            $this->payable = $personalized_salary;

            return $this;
        }

        $attributes = $this->monthly_basic ? ['monthly_basic' => $this->monthly_basic] : [];

        $this->payable = $personalized_salary ?? factory(PersonalizedSalary::class)->create($attributes);

        return $this;
    }

    public function withStructuredSalary(StructuredSalary $structured_salary = null)
    {
        if($structured_salary){
            $this->payable = $structured_salary;
            return $this;
        }

        if(! $this->monthly_basic){
            $this->payable = factory(StructuredSalary::class)->create();

            return $this;
        }

        $cadre_step = factory(CadreStep::class)->create(['monthly_basic' => $this->monthly_basic]);

        $this->payable = factory(StructuredSalary::class)->create(['cadre_step_id' => $cadre_step->id]);

        return $this;
    }

    public function withMonthlyBasicOf(float $monthly_basic)
    {
        $this->monthly_basic = $monthly_basic;

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

    public function withAllowances($allowance_count = 0)
    {
        $this->allowance_count = $allowance_count;

        return $this;
    }

    public function create($override = [])
    {
        \Facades\BeneficiaryFactory::clearResolvedInstance('BeneficiaryFactory');

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

        if($this->allowance_count){
            factory(AllowanceDetail::class, $this->allowance_count)->create(['beneficiary_id' => $beneficiary->id]);
        }

        return $beneficiary;
    }
}
