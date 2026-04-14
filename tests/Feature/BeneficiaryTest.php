<?php

namespace Tests\Feature;

use Facades\Tests\Setup\BeneficiaryTestFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeneficiaryTest extends TestCase
{
    use RefreshDatabase;

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

}
