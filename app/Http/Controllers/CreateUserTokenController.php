<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class CreateUserTokenController extends Controller
{
    public function create()
    {
        $user = User::find(1);

        $token = $user->createToken('state_airs');

        return $token->plainTextToken;
    }
}
