<?php

namespace Tests\Feature;

use App\Domain;
use App\Deduction;
use App\ValueType;
use App\DeductionName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function factory;
use function random_int;

class DeductionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_deduction()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $deduction_name = factory(DeductionName::class)->create();
        $value_type = factory(ValueType::class)->create();
        $amount = random_int(100, 100000);

        $attributes = [
            'deduction_name_id' => $deduction_name,
            'value_type_id' => $value_type,
            'amount' => $amount,
        ];

        $this->post(route('deductions.store'), $attributes);

        $this->assertDatabaseHas('deductions', $attributes);
    }
}
