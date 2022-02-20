<?php

namespace Tests\Feature;

use App\Compute\Tax;
use App\PersonalizedSalary;
use Facades\Tests\Setup\BeneficiaryTestFactory as BeneficiaryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ComputeTaxTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_computes_beneficiary_tax()
    {
        $personalizedSalary = factory(PersonalizedSalary::class)->create(['monthly_basic' => 100000]);

        $beneficiary = BeneficiaryFactory::withPersonalizedSalary($personalizedSalary)->create();

        $tax = (new Tax())->compute($beneficiary);

        $this->assertEquals(4261.67, $tax);
    }
}
