<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CreateUserTokenController extends Controller
{
    public function create()
    {
        $registration_token = request()->query('create_token');
        $token = base64_encode(hash('sha1', 'anambra'));

        //        dd($token);

        if ($registration_token != $token) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $user = Auth::user();

        //        $token = $user->createToken('state_airs');
        //        $token = $user->createToken('loans');
        $token = $user->createToken('fidelity');

        return $token->plainTextToken;
    }
}
