<?php

namespace App\Policies;

use App\Models\MicroFinanceBank;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MicrofinancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, MicroFinanceBank $mfb)
    {
        return $user->microfinanceBank->micro_finance_bank_id === $micro_finance_bank->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, MicroFinanceBank $micro_finance_bank)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, MicroFinanceBank $micro_finance_bank)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, MicroFinanceBank $micro_finance_bank)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, MicroFinanceBank $micro_finance_bank)
    {
        //
    }
}
