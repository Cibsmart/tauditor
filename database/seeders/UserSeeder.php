<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            'first_name'        => 'State',
            'last_name'         => 'Admin',
            'email'             => 'ansgadmin@payroll.com',
            'email_verified_at' => now(),
            'password'          => 'password',
            'domain_id'         => 'state',
            'remember_token'    => Str::random(10),
        ]);

        $user->assignRole('super_admin');

        $user = factory(User::class)->create([
            'first_name'        => 'Jaac',
            'last_name'         => 'Admin',
            'email'             => 'jaacadmin@payroll.com',
            'email_verified_at' => now(),
            'password'          => 'password',
            'domain_id'         => 'jaac',
            'remember_token'    => Str::random(10),
        ]);

        $user->assignRole('super_admin');
    }
}
