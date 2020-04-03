<?php

namespace Tests\Feature;

use App\Payroll;
use Carbon\Carbon;
use RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function route;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp(); //

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    /** @test */
    public function anUnauthenticatedUserCannotGeneratePayroll()
    {
        $response = $this->post(route('payroll.store'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function anUnauthorizedUserCannotGeneratePayroll()
    {
        $this->signIn();

        //An the user sends a request to run payroll
        $response = $this->post(route('payroll.store'));

        $response->assertStatus(403);
    }

    /** @test */
    public function anAuthorizedCanCreatePayroll()
    {
        //Given we have an authorized user
        $this->signIn()->withoutExceptionHandling();

        $date = Carbon::now();
        $user = Auth::user();

        $user->givePermissionTo('generate_payroll');

        $attributes = [
            'month' => $date->month,
            'year' => $date->year,
            'user_id' => $user->id,
            'domain_id' => $user->domain->id,
        ];

        //An the user sends a request to run payroll
        $this->post(route('payroll.store'));

        //Assert that database has a payroll
        $this->assertEquals(1, Payroll::count());
        $this->assertDatabaseHas('payrolls', $attributes);
    }

    /** @test */
    public function payrollCanOnBeGeneratedOnceInAMonth()
    {
        $this->signIn();; //->withoutExceptionHandling();

        $date = Carbon::now();
        $user = Auth::user();

        $user->givePermissionTo('generate_payroll');

        $attributes = [
            'month' => $date->month,
            'year' => $date->year,
            'user_id' => $user->id,
            'domain_id' => $user->domain->id,
        ];

        $this->post(route('payroll.store'));

        $this->assertEquals(1, Payroll::count());
        $this->assertDatabaseHas('payrolls', $attributes);

        $response = $this->post(route('payroll.store'));

        $response->assertSessionHas('message');
    }

    /** @test */
    public function aPayrollHasPaySchedules()
    {
        $this->signIn()->withoutExceptionHandling();

        $date = Carbon::now();
        $user = Auth::user();

        $user->givePermissionTo('generate_payroll');

        $attributes = [
            'month' => $date->month,
            'year' => $date->year,
            'user_id' => $user->id,
            'domain_id' => $user->domain->id,
        ];

        //An the user sends a request to run payroll
        $response = $this->post(route('payroll.store'));

        $payroll = $response->original;
        $this->assertDatabaseHas('pay_schedules', ['payroll_id', $payroll->id]);
    }
}
