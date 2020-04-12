<?php

namespace Tests\Feature;

use App\Domain;
use App\Deduction;
use App\ValueType;
use App\DeductionName;
use App\DeductionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function dump;
use function route;
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
            'deduction_name' => $deduction_name->id,
            'value_type' => $value_type->id,
            'value' => $amount,
        ];

        $this->post(route('deductions.store'), $attributes)
             ->assertSessionHas('success');

        $this->assertDatabaseHas('deductions', ['deduction_name_id' => $deduction_name->id]);
    }

    /** @test */
    public function it_can_create_deduction_after_creating_new_deduction_name()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $deduction_type = factory(DeductionType::class)->create(['domain_id' => $user->id]);
        $value_type = factory(ValueType::class)->create(['code' => 'fixed']);
        $amount = random_int(100, 100000);

        $attributes = [
            'deduction_type' => $deduction_type->id,
            'deduction_name' => -1,
            'value_type' => $value_type->id,
            'value' => $amount,
            'new_deduction' => 'Max'
        ];

        $this->post(route('deductions.store'), $attributes)
             ->assertSessionHas('success');

        $this->assertCount(1, $user->deductions);
    }
}
