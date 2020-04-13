<?php

namespace Tests\Feature;

use App\Mda;
use App\Payroll;
use Carbon\Carbon;
use Tests\TestCase;
use App\PaySchedule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function factory;

class MdaScheduleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_view_mda_pay_schedules_of_payroll_yet_to_be_generated()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $payroll = factory(Payroll::class)->create();

        $this->get(route('mda_schedules.index', $payroll))
             ->assertSessionHas('error');
    }


    /** @test */
    public function can_view_mda_pay_schedules()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $payroll = factory(Payroll::class)->create(['domain_id' => $user->domain_id, 'generated' => Carbon::now()]);

        //Given we have pay schedules with the same mda
        $first_mda = factory(Mda::class)->create();
        factory(PaySchedule::class, 10)->create(['payroll_id' => $payroll->id, 'mda_id' => $first_mda->id]);

        //Given we have pay schedules with the same mda
        $second_mda = factory(Mda::class)->create();
        factory(PaySchedule::class, 10)->create(['payroll_id' => $payroll->id, 'mda_id' => $second_mda->id]);

        //When we hit the mda schedules index endpoint we should have all schedule group by mda
        $this->get(route('mda_schedules.index', $payroll))
             ->assertSuccessful()
             ->assertPropCount('schedules.data', 2)
             ->assertPropValue('schedules.data', function ($schedules) {
                 $this->assertEquals(
                     ['payroll_id', 'mda_id', 'mda_name', 'total_amount', 'head_count', 'month', 'year', 'domain', 'pensioner'],
                     array_keys($schedules[0]));
             });
    }
}
