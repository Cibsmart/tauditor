<?php

namespace Tests\Feature;

use App\Domain;
use App\Payroll;
use App\Beneficiary;
use App\Actions\RunPayrollAction;
use Facades\Tests\Setup\BeneficiaryTestFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function dump;
use function route;
use function factory;

class RunPayrollTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthorized_user_cannot_run_payroll()
    {
        $payroll = factory(Payroll::class)->create();

        $this->post(route('actions.run_payroll', ['payroll' => $payroll]))
             ->assertRedirect(route('login'));

        $this->signIn();

        $this->post(route('actions.run_payroll', ['payroll' => $payroll]))
             ->assertStatus(403);
    }

    /** @test */
    public function an_authorized_user_can_run_payroll()
    {
        $this->withoutExceptionHandling();

        $this->signIn()->givePermissionTo('run_payroll');

        $payroll = factory(Payroll::class)->create();

        $this->post(route('actions.run_payroll', ['payroll' => $payroll]))
             ->assertSessionHas('success')
             ->assertRedirect(route('payroll.index'));
    }

    /** @test */
    public function executing_run_payroll_action_generates_pay_schedules_for_only_active_users()
    {
        $domain = factory(Domain::class)->create();

        //Given we have active Beneficiary
        BeneficiaryTestFactory::ActiveState()->withMonthlyBasic(100000)->withStructuredSalary()
                              ->withValuableAmount(500)->withAllowances(5)->withDeductions(5)
                              ->withBank()->withMda()->create(['domain_id' => $domain->id]);

        //And an Inactive Beneficiary
        BeneficiaryTestFactory::InactiveState()->withMonthlyBasic(100000)->withStructuredSalary()
                               ->withValuableAmount(500)->withAllowances(5)->withDeductions(5)
                               ->withBank()->withMda()->create(['domain_id' => $domain->id]);

        //Given we have a Payroll
        $payroll = factory(Payroll::class)->create(['domain_id' => $domain->id]);

        //When RunPayAction is executed
        (new RunPayrollAction())->execute($payroll);

        //We should see pay schedules for only active users in the Database
        $this->assertEquals(Beneficiary::active()->count(), $payroll->schedules->count());
        $this->assertLessThan(Beneficiary::count(), $payroll->schedules->count());
    }
}
