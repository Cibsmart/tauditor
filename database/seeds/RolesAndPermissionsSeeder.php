<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
            'create_payroll',
            'create_allowance',
            'create_deduction',
            'create_beneficiaries',

            'assign_allowance',
            'assign_deduction',

            'view_report',
            'view_autopay',
            'view_payroll',
            'view_pay_slip',
            'view_analysis',
            'view_dashboard',
            'view_mfb_schedule',
            'view_pay_schedule',
            'view_schedule',
            'view_beneficiaries',
            'view_mda_report',
            'view_payment_summary',
            'view_category_report',
            'view_beneficiary_report',

            'run_payroll',
            'approve_updates',
            'edit_beneficiaries',
            'download_mfb_schedule',
            'update_salary_structure',

            //Users, Roles & Permissions
            'view_users',
            'create_users',
            'create_super_admin',
            'create_admin',
            'create_hod',
            'manage_users',
            'assign_roles',
            'assign_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin->givePermissionTo(
            'view_beneficiaries',
            'create_beneficiaries',
            'edit_beneficiaries',
            'create_allowance',
            'create_deduction',
            'assign_allowance',
            'assign_deduction',
            'update_salary_structure',
            'create_payroll',
            'run_payroll',
            'view_pay_slip',
            'view_mfb_schedule',
            'download_mfb_schedule',
            'view_pay_schedule',
            'view_payroll',
            'view_dashboard',
            'view_schedule',
            'view_analysis',
            'view_autopay',
            'view_report',
            'view_mda_report',
            'view_payment_summary',
            'view_category_report',
            'view_beneficiary_report',
        );

        $hod->givePermissionTo($permissions);

        $payroll_officer->givePermissionTo('view_beneficiaries', 'view_pay_schedule');

        $mfb->givePermissionTo('view_mfb_schedule', 'download_mfb_schedule');
    }
}
