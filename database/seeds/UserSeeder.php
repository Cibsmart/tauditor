<?php

use App\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(User::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@payroll.com',
            'email_verified_at' => now(),
            'password' => 'password',
            'domain_id' => 1,
            'remember_token' => Str::random(10),
        ]);

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //Roles
        $super_admin = Role::create(['name' => 'super_admin']);
        $admin = Role::create(['name' => 'admin']);
        $hod = Role::create(['name' => 'hod']);
        $payroll_officer = Role::create(['name' => 'payroll_officer']);
        $mfb = Role::create(['name' => 'mfb']);
        $beneficiary = Role::create(['name' => 'beneficiary']);

        //Permissions
        $permissions = [
            'view_beneficiaries',
            'create_beneficiaries',
            'edit_beneficiaries',
            'create_allowance',
            'create_deduction',
            'assign_allowance',
            'assign_deduction',
            'update_salary_structure',
            'approve_updates',
            'view_pay_slip',
            'view_mfb_schedule',
            'download_mfb_schedule',
            'view_pay_schedule'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin->givePermissionTo('view_beneficiaries', 'create_beneficiaries', 'edit_beneficiaries',
            'create_allowance', 'create_deduction', 'assign_allowance', 'assign_deduction', 'update_salary_structure',
            'view_pay_slip', 'view_mfb_schedule', 'download_mfb_schedule', 'view_pay_schedule');

        $hod->givePermissionTo($permissions);

        $payroll_officer->givePermissionTo('view_beneficiaries', 'view_pay_schedule');

        $mfb->givePermissionTo('view_mfb_schedule', 'download_mfb_schedule');

        $user->assignRole('super_admin');
    }
}
