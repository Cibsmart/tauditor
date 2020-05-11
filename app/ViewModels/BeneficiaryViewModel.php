<?php


namespace App\ViewModels;

use App\User;
use App\State;
use App\Gender;
use App\Beneficiary;
use App\MaritalStatus;
use App\LocalGovernment;

class BeneficiaryViewModel
{
    public User $user;

    public ?Beneficiary $beneficiary;

    public function __construct(User $user, Beneficiary $beneficiary = null)
    {
        $this->user = $user;
        $this->beneficiary = $beneficiary;
    }

    public function data()
    {
        $beneficiary = $this->beneficiary ?? new Beneficiary();

        return [
            'beneficiary' => $beneficiary,
            'local_governments' => LocalGovernment::all()->toArray(),
            'states' => State::all()->toArray(),
            'genders' => Gender::all()->toArray(),
            'marital_statues' => MaritalStatus::all()->toArray(),
            'beneficiary_types' => $this->user->domain->beneficiaryTypes->toArray(),
        ];
    }
}
