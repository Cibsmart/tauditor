<?php

namespace Tests\Feature;

use App\Mda;
use App\Payroll;
use App\PaySchedule;
use function array_keys;
use Carbon\Carbon;
use function compact;
use function factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use function route;
use Tests\TestCase;

class PayScheduleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_view_pay_schedules_of_payroll_yet_to_be_generated()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $payroll = factory(Payroll::class)->create();

        $mda = factory(Mda::class)->create();

        $this->get(route('pay_schedules.index', compact('payroll', 'mda')))
             ->assertSessionHas('error');
    }

    /** @test */
    public function can_view_mda_pay_schedules()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $payroll = factory(Payroll::class)->create(['domain_id' => $user->domain_id, 'generated' => Carbon::now()]);

        //Given we have pay schedules with the same mda
        $mda = factory(Mda::class)->create();
        factory(PaySchedule::class, 10)->create(['payroll_id' => $payroll->id, 'mda_id' => $mda->id]);

        //When we hit the pay schedules index endpoint we should see all schedule in for the specific mda
        $this->get(route('pay_schedules.index', compact('payroll', 'mda')))
             ->assertSuccessful()
             ->assertPropCount('schedules.data', 10)
             ->assertPropValue('schedules.data', function ($schedules) {
                 $this->assertEquals(
                     ['id', 'beneficiary_name', 'beneficiary_code', 'mda', 'sub_mda', 'sub_sub_mda', 'account_number', 'bank_name', 'net_pay'],
                     array_keys($schedules[0])
                 );
             });
    }
}
