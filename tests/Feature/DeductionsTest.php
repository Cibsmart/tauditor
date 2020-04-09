<?php

namespace Tests\Feature;

use App\Domain;
use App\Deduction;
use App\ValueType;
use App\DeductionName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function dump;
use function factory;
use function random_int;

class DeductionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_deduction()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $deduction_name = factory(DeductionName::class)->create(['name' => 'SH', 'code' => 'SH', 'domain_id' => $user->domain_id]);
        $value_type = factory(ValueType::class)->create(['code' => 'fixed']);
        $amount = random_int(100, 100000);

        $attributes = [
            'deduction_type' => $deduction_name->deduction_type_id,
            'deduction_name' => $deduction_name->name,
            'value_type' => $value_type->code,
            'amount' => $amount,
        ];

        $this->post(route('deductions.store'), $attributes)
             ->assertSessionHas('success');

        $this->assertDatabaseHas('deductions', ['deduction_name_id' => $deduction_name->id]);
    }
}
