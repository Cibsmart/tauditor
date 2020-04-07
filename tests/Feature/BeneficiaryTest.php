<?php

namespace Tests\Feature;

use App\CadreStep;
use App\Allowance;
use App\StructuredSalary;
use App\PersonalizedSalary;
use Facades\Tests\Setup\BeneficiaryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function round;
use function factory;
use const PHP_ROUND_HALF_UP;

class BeneficiaryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function beneficiary_with_structured_salary_has_basic_pay()
    {

        $beneficiary = BeneficiaryFactory::withMonthlyBasicOf($monthly_basic = 100000.12)
                                         ->withStructuredSalary()
                                         ->create();

        $this->assertEquals($monthly_basic, $beneficiary->basic());
    }

    /** @test */
    public function structured_salary_basic_pay_is_rounded_half_up_to_two_decimal_point()
    {
        $beneficiary = BeneficiaryFactory::withMonthlyBasicOf($monthly_basic = 100000.12945454)
                                         ->withStructuredSalary()
                                         ->create();

        $this->assertEquals(round($monthly_basic, 2), $beneficiary->basic());
    }

    /** @test */
    public function beneficiary_with_personalized_salary_has_basic_pay()
    {
        $beneficiary = BeneficiaryFactory::withMonthlyBasicOf($monthly_basic = 85000)
                                         ->withPersonalizedSalary()
                                         ->create();

        $this->assertEquals($monthly_basic, $beneficiary->basic());
    }

    /** @test */
    public function personalized_salary_basic_pay_is_rounded_half_up_to_two_decimal_point()
    {
        $beneficiary = BeneficiaryFactory::withMonthlyBasicOf($monthly_basic = 50000.12945454)
                                         ->withPersonalizedSalary()
                                         ->create();

        $this->assertEquals(round($monthly_basic, 2), $beneficiary->basic());
    }

    /** @test */
    public function beneficiary_computes_sum_of_total_allowances()
    {
        $beneficiary = BeneficiaryFactory::withAllowances(5)->create();

        $this->assertNotEquals(0, $beneficiary->monthlyAllowance());
    }
}
