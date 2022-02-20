<?php

namespace Tests\Setup;

use App\AllowanceDetail;
use App\Bank;
use App\BankDetail;
use App\Beneficiary;
use App\BeneficiaryStatus;
use App\CadreStep;
use App\DeductionDetail;
use App\MdaDetail;
use App\MicroFinanceBank;
use App\NextOfKin;
use App\PersonalizedSalary;
use App\Qualification;
use App\SalaryDetail;
use App\StructuredSalary;
use App\WorkDetail;
use Facades\BeneficiaryFactory;
use function factory;
use Faker\Generator;

class BeneficiaryTestFactory
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

    private ?int $deduction_count = null;

    private ?float $valuable_amount = null;

    private int $beneficiary_status = 0;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * @param  MicroFinanceBank|null  $micro_finance_bank
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

    /**
     * @param  PersonalizedSalary|null  $personalized_salary
     * @return $this
     */
    public function withPersonalizedSalary(PersonalizedSalary $personalized_salary = null)
    {
        if ($personalized_salary) {
            $this->payable = $personalized_salary;

            return $this;
        }

        $attributes = $this->monthly_basic ? ['monthly_basic' => $this->monthly_basic] : [];

        $this->payable = $personalized_salary ?? factory(PersonalizedSalary::class)->create($attributes);

        return $this;
    }

    public function withStructuredSalary(StructuredSalary $structured_salary = null)
    {
        if ($structured_salary) {
            $this->payable = $structured_salary;

            return $this;
        }

        if (! $this->monthly_basic) {
            $this->payable = factory(StructuredSalary::class)->create();

            return $this;
        }

        $cadre_step = factory(CadreStep::class)->create(['monthly_basic' => $this->monthly_basic]);

        $this->payable = factory(StructuredSalary::class)->create(['cadre_step_id' => $cadre_step->id]);

        return $this;
    }

    public function withMonthlyBasic(float $monthly_basic)
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

    public function withDeductions($deduction_count = 0)
    {
        $this->deduction_count = $deduction_count;

        return $this;
    }

    public function withValuableAmount(float $valuable_amount)
    {
        $this->valuable_amount = $valuable_amount;

        return $this;
    }

    public function activeState()
    {
        $this->beneficiary_status = 1;

        return $this;
    }

    public function inactiveState()
    {
        $this->beneficiary_status = 0;

        return $this;
    }

    public function create($override = [])
    {
        BeneficiaryFactory::clearResolvedInstance('BeneficiaryTestFactory');

        $beneficiary = factory(Beneficiary::class)->create($override);

        $beneficiary->status()->save(factory(BeneficiaryStatus::class)->make([
            'beneficiary_id' => $beneficiary->id, 'active' => $this->beneficiary_status,
        ]));

        if ($this->bank) {
            $this->bank->beneficiaries()->save(factory(BankDetail::class)->make([
                'beneficiary_id' => $beneficiary->id,
            ]));
        }

        if ($this->payable) {
            $this->payable->salary()->save(factory(SalaryDetail::class)->make(['beneficiary_id' => $beneficiary->id]));
        }

        if ($this->next_of_kin) {
            $beneficiary->nextOfKin()->save(factory(NextOfKin::class)->make(['beneficiary_id' => $beneficiary->id]));
        }

        $beneficiary->qualifications()->saveMany(factory(Qualification::class, $this->qualification_count)->make());

        if ($this->mda) {
            $beneficiary->mdaDetail()->save(factory(MdaDetail::class)->make(['beneficiary_id' => $beneficiary->id]));
        }

        if ($this->work_detail) {
            $beneficiary->workDetail()->save(factory(WorkDetail::class)->make());
        }

        if ($this->allowance_count) {
            $attributes = $this->valuable_amount
                ? ['beneficiary_id' => $beneficiary->id, 'amount' => $this->valuable_amount]
                : ['beneficiary_id' => $beneficiary->id];

            factory(AllowanceDetail::class, $this->allowance_count)->create($attributes);
        }

        if ($this->deduction_count) {
            $attributes = $this->valuable_amount
                ? ['beneficiary_id' => $beneficiary->id, 'amount' => $this->valuable_amount]
                : ['beneficiary_id' => $beneficiary->id];

            factory(DeductionDetail::class, $this->deduction_count)->create($attributes);
        }

        return $beneficiary;
    }
}
