<?php

namespace Tests\Feature;

use App\Payroll;
use function array_merge;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\Setup\UserFactory;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    private array $attributes;

    protected function setUp() : void
    {
        parent::setUp();

        $date = Carbon::now();

        $this->attributes = [
            'month' => $date->month,
            'month_name' => $date->monthName,
            'year' => $date->year,
        ];
    }

    /** @test */
    public function anUnauthenticatedUserCannotCreatePayroll()
    {
        $response = $this->post(route('payroll.store'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function anUnauthorizedUserCannotCreatePayroll()
    {
        $this->signIn();

        $response = $this->post(route('payroll.store'));

        $response->assertStatus(403);
    }

    /** @test */
    public function anAuthorizedCanCreatePayroll()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn()->givePermissionTo('create_payroll');

        $attributes = array_merge($this->attributes, ['domain_id' => $user->domain->id]);

        //An the user sends a request to run payroll
        $this->post(route('payroll.store'));

        //Assert that database has a payroll
        $this->assertEquals(1, Payroll::count());
        $this->assertDatabaseHas('payrolls', $attributes);
    }

    /** @test */
    public function payrollCanOnlyBeGeneratedOnceInAMonth()
    {
        $user = $this->signIn()->givePermissionTo('create_payroll');

        $attributes = array_merge($this->attributes, ['domain_id' => $user->domain->id]);

        $this->post(route('payroll.store'));

        $this->assertEquals(1, Payroll::count());
        $this->assertDatabaseHas('payrolls', $attributes);

        $response = $this->post(route('payroll.store'));

        $response->assertSessionHas('error');
    }

    /** @test */
    public function anAuthorizedUserCanViewPayroll()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn()->givePermissionTo('view_payroll');

        $user->domain->payrolls()->saveMany(factory(Payroll::class, 5)->make());

        $this->get(route('payroll.index'))
             ->assertStatus(200)
             ->assertPropCount('payrolls.data', 5)
             ->assertPropValue('payrolls.data', function ($payrolls) {
                 $this->assertEquals(
                     ['id', 'month', 'year', 'approved', 'archived', 'generated', 'generated_by'],
                     array_keys($payrolls[0])
                 );
             });
    }
}
