<?php

namespace Tests\Setup;

use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\Beneficiary;
use App\Models\BeneficiaryStatus;
use App\Models\MicroFinanceBank;
use App\Models\NextOfKin;
use App\Models\PersonalizedSalary;
use App\Models\Qualification;
use App\Models\SalaryDetail;
use App\Models\WorkDetail;
use Facades\BeneficiaryFactory;
use Faker\Generator;

use function factory;

class BeneficiaryTestFactory
{
    private Generator $faker;

    private $bank = null;

    private $payable = null;

    private bool $next_of_kin = false;

    private bool $work_detail = false;

    private int $qualification_count = 0;

    private ?float $monthly_basic = null;

    private int $beneficiary_status = 0;

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

    /**
     * @param PersonalizedSalary|null $personalized_salary
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

        if ($this->work_detail) {
            $beneficiary->workDetail()->save(factory(WorkDetail::class)->make());
        }

        return $beneficiary;
    }
}
