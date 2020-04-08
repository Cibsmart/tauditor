<?php

namespace Tests\Feature;

use Facades\Tests\Setup\BeneficiaryTestFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class BeneficiaryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function beneficiary_with_structured_salary_has_basic_pay()
    {
        $beneficiary = BeneficiaryTestFactory::withMonthlyBasic($monthly_basic = 100000.12)
                                         ->withStructuredSalary()
                                         ->create();

        $this->assertEquals($monthly_basic, $beneficiary->basic());
    }

    /** @test */
    public function structured_salary_basic_pay_is_rounded_half_up_to_two_decimal_point()
    {
        $beneficiary = BeneficiaryTestFactory::withMonthlyBasic($monthly_basic = 100000.12945454)
                                         ->withStructuredSalary()
                                         ->create();

        $this->assertEquals(round($monthly_basic, 2), $beneficiary->basic());
    }

    /** @test */
    public function beneficiary_with_personalized_salary_has_basic_pay()
    {
        $beneficiary = BeneficiaryTestFactory::withMonthlyBasic($monthly_basic = 85000)
                                         ->withPersonalizedSalary()
                                         ->create();

        $this->assertEquals($monthly_basic, $beneficiary->basic());
    }

    /** @test */
    public function personalized_salary_basic_pay_is_rounded_half_up_to_two_decimal_point()
    {
        $beneficiary = BeneficiaryTestFactory::withMonthlyBasic($monthly_basic = 50000.12945454)
                                         ->withPersonalizedSalary()
                                         ->create();

        $this->assertEquals(round($monthly_basic, 2), $beneficiary->basic());
    }

    /** @test */
    public function beneficiary_computes_sum_of_total_allowances()
    {
        $beneficiary = BeneficiaryTestFactory::withValuableAmount($amount = 500)
                                         ->withAllowances($number = 5)
                                         ->create();

        $sum = round($amount, 2) * $number;

        $this->assertEquals($sum, $beneficiary->totalMonthlyAllowance());
    }

    /** @test */
    public function beneficiary_computes_sum_of_total_deductions()
    {
        $beneficiary = BeneficiaryTestFactory::withValuableAmount($amount = 500)
                                         ->withDeductions($number = 5)
                                         ->create();

        $sum = round($amount, 2) * $number;

        $this->assertEquals($sum, $beneficiary->totalMonthlyDeduction());
    }

    /** @test */
    public function beneficiary_can_retrieve_applied_allowances()
    {
        $beneficiary = BeneficiaryTestFactory::withAllowances($number = 5)
                                             ->create();

        $this->assertEquals($number, $beneficiary->allowances()->count());
    }

    /** @test */
    public function beneficiary_can_retrieve_applied_deductions()
    {
        $beneficiary = BeneficiaryTestFactory::withDeductions($number = 5)
                                             ->create();

        $this->assertEquals($number, $beneficiary->deductions()->count());
    }
}
