<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PotentialUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

use function is_null;
use function redirect;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = 'dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'domain_id' => ['required', 'string', 'exists:domains,id'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users')
                    ->where(fn ($query) => $query->where('domain_id', $data['domain_id'])),
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'domain_id' => $data['domain_id'],
        ]);

        $this->assignRoleAndDisableUserRegistrationLink($user);

        return $user;
    }

    public function showRegistrationForm()
    {
        $registration_token = request()->query('registration_token');

        if (! Str::isUuid($registration_token)) {
            return redirect()->back()->with('error', 'Invalid Link');
        }

        $user = PotentialUser::query()->firstWhere('uuid', $registration_token);

        if (is_null($user)) {
            return redirect()->back()->with('error', 'Invalid Link');
        }

        $domain = $user->domain;

        return Inertia::render('Auth/Register', [
            'domain' => ['id' => $domain->id, 'name' => $domain->name],
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ],
        ]);
    }

    protected function assignRoleAndDisableUserRegistrationLink(User $user)
    {
        $p_user = PotentialUser::query()
            ->where('email', $user->email)
            ->where('domain_id', $user->domain_id)
            ->first();

        $user->assignRole($p_user->role_id);

        if ($p_user->microfinanceBank) {
            $user->microfinanceBank()->create([
                'micro_finance_bank_id' => $p_user->microfinanceBank->micro_finance_bank_id,
            ]);
        }

        $p_user->registered = Carbon::now();

        $p_user->save();

        $p_user->delete();
    }
}
