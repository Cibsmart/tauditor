<?php


namespace App\ViewModels;

use App\User;
use App\State;
use App\Gender;
use App\MaritalStatus;
use App\LocalGovernment;

class CreateBeneficiaryData
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function data()
    {
        return [
            'lga' => LocalGovernment::all()->toArray(),
            'states' => State::all()->toArray(),
            'gender' => Gender::all()->toArray(),
            'marital_status' => MaritalStatus::all()->toArray(),
            'beneficiary_types' => $this->user->domain->beneficiaryTypes->toArray(),
        ];
    }
}
