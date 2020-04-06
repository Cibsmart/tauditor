<?php

namespace Tests\Feature;

use App\Compute\Tax;
use App\PersonalizedSalary;
use Facades\Tests\Setup\BeneficiaryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function factory;

class ComputeTaxTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itComputesBeneficiaryTax()
    {
        $personalizedSalary = factory(PersonalizedSalary::class)->create(['monthly_basic' => 100000]);

        $beneficiary = BeneficiaryFactory::withPersonalizedSalary($personalizedSalary)->create();

        $tax = (new Tax())->compute($beneficiary);

        $this->assertEquals(4261.67, $tax);
    }
}
