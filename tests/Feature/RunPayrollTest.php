<?php

namespace Tests\Feature;

use App\Payroll;
use App\Compute\Tax;
use App\Actions\ComputeTaxAction;
use Facades\Tests\Setup\BeneficiaryFactory;
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
    public function anUnauthorizedUserCannotRunPayroll()
    {
        $payroll = factory(Payroll::class)->create();

        $this->post(route('actions.run_payroll', ['payroll' => $payroll]))
             ->assertRedirect(route('login'));

        $this->signIn();

        $this->post(route('actions.run_payroll', ['payroll' => $payroll]))
             ->assertStatus(403);
    }

    /** @test */
    public function anAuthorizedUserCanRunPayroll()
    {
        $this->withoutExceptionHandling();

        $this->signIn()->givePermissionTo('run_payroll');

        $payroll = factory(Payroll::class)->create();

        $this->post(route('actions.run_payroll', ['payroll' => $payroll]))
             ->assertSessionHas('success')
             ->assertRedirect(route('payroll.index'));

        $this->assertNotEquals(0, $payroll->schedules->count());
    }

    /** @test */
    public function example()
    {
        $beneficiary = BeneficiaryFactory::withPersonalizedSalary()
                                         ->create();

        $tax = (new Tax())->compute($beneficiary);

        dump($beneficiary->basic() * 12);
        dump($beneficiary->monthlyAllowance() * 12);
        dump($tax);

        $this->assertTrue(true);
    }
}
