<?php

namespace App\Policies;

use App\User;
use App\Payroll;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayrollPolicy
{
    use HandlesAuthorization;


    public function create(User $user)
    {
        return $user->can('generate_payroll');
    }
}
