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
            'domain_id' => 'state',
            'remember_token' => Str::random(10),
        ]);

        $user->assignRole('super_admin');

        $user = factory(User::class)->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@payroll.com',
            'email_verified_at' => now(),
            'password' => 'password',
            'domain_id' => 'jaac',
            'remember_token' => Str::random(10),
        ]);

        $user->assignRole('super_admin');
    }
}
