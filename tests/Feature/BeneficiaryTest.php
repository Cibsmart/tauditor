<?php

namespace Tests\Feature;

use Facades\Tests\Setup\BeneficiaryTestFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeneficiaryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function beneficiaryWithStructuredSalaryHasBasicPay()
    {
        $beneficiary = BeneficiaryTestFactory::withMonthlyBasic($monthly_basic = 100000.12)
                                             ->withStructuredSalary()
                                             ->create();

        $this->assertEquals($monthly_basic, $beneficiary->basic());
    }

    /** @test */
    public function structuredSalaryBasicPayIsRoundedHalfUpToTwoDecimalPoint()
    {
        $beneficiary = BeneficiaryTestFactory::withMonthlyBasic($monthly_basic = 100000.12945454)
                                             ->withStructuredSalary()
                                             ->create();

        $this->assertEquals(round($monthly_basic, 2), $beneficiary->basic());
    }

    /** @test */
    public function beneficiaryWithPersonalizedSalaryHasBasicPay()
    {
        $beneficiary = BeneficiaryTestFactory::withMonthlyBasic($monthly_basic = 85000)
                                             ->withPersonalizedSalary()
                                             ->create();

        $this->assertEquals($monthly_basic, $beneficiary->basic());
    }

    /** @test */
    public function personalizedSalaryBasicPayIsRoundedHalfUpToTwoDecimalPoint()
    {
        $beneficiary = BeneficiaryTestFactory::withMonthlyBasic($monthly_basic = 50000.12945454)
                                             ->withPersonalizedSalary()
                                             ->create();

        $this->assertEquals(round($monthly_basic, 2), $beneficiary->basic());
    }

    /** @test */
    public function beneficiaryComputesSumOfTotalDeductions()
    {
        $beneficiary = BeneficiaryTestFactory::withValuableAmount($amount = 500)
                                             ->withDeductions($number = 5)
                                             ->create();

        $sum = round($amount, 2) * $number;

        $this->assertEquals($sum, $beneficiary->totalMonthlyDeduction());
    }

    /** @test */
    public function beneficiaryCanRetrieveAppliedDeductions()
    {
        $beneficiary = BeneficiaryTestFactory::withDeductions($number = 5)
                                             ->create();

        $this->assertEquals($number, $beneficiary->deductions()->count());
    }
}
