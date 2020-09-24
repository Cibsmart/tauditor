<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MicroFinanceBank;
use Illuminate\Auth\Access\HandlesAuthorization;
use function dd;

class MicroFinanceBankPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  MicroFinanceBank  $mfb
     * @return mixed
     */
    public function view(User $user, MicroFinanceBank $mfb)
    {
        return $user->can('view_mfb_schedule')
            && $user->microfinanceBank->micro_finance_bank_id === $mfb->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MicroFinanceBank  $MicroFinanceBank
     * @return mixed
     */
    public function update(User $user, MicroFinanceBank $MicroFinanceBank)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MicroFinanceBank  $MicroFinanceBank
     * @return mixed
     */
    public function delete(User $user, MicroFinanceBank $MicroFinanceBank)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MicroFinanceBank  $MicroFinanceBank
     * @return mixed
     */
    public function restore(User $user, MicroFinanceBank $MicroFinanceBank)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MicroFinanceBank  $MicroFinanceBank
     * @return mixed
     */
    public function forceDelete(User $user, MicroFinanceBank $MicroFinanceBank)
    {
        //
    }
}
