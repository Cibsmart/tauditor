<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Domain;
use App\Mail\Registration;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PotentialUser;
use Illuminate\Validation\Rule;
use App\Models\MicroFinanceBank;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\AccountCreated;
use function redirect;

class ManageUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $users = (object) [];
        $new_users = [];

        $roles = $this->getRoles();

        if ($request->has('role')) {
            $users = $this->getUsers($request->role);
            $new_users = $this->getNewUsers($request->role);
        }

        return Inertia::render('ManageUsers/Index', [
            'roles'     => $roles,
            'users'     => $users,
            'new_users' => $new_users,
        ]);
    }

    public function create()
    {
        $domain = Auth::user()->domain;

        $black_list = $this->getRolesUserCannotCreate();

        $roles = $this->getRoles($black_list);

        $mfbs = $this->getMicrofinanceBanks($domain);

        return Inertia::render('ManageUsers/Create', [
            'mfbs'   => $mfbs,
            'roles'  => $roles,
            'domain' => $domain->name,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $domain = $user->domain;

        $request->validate([
            'first_name'        => ['required', 'string',],
            'last_name'         => ['required', 'string',],
            'email'             => [
                'required',
                'email:filter',
                Rule::unique('users')
                    ->where(fn ($query) => $query->where('domain_id', $domain->id)),
            ],
            'role'              => ['required', 'integer'],
            'microfinance_bank' => ['sometimes', 'required', 'integer',],
        ]);

        $new_user = $domain->potentialUser()
                           ->create([
                               'uuid'       => Str::uuid(),
                               'first_name' => $request->first_name,
                               'last_name'  => $request->last_name,
                               'email'      => $request->email,
                               'role_id'    => $request->role,
                               'user_id' => $user->id,
                           ]);

        if ($request->has('microfinance_bank')) {
            $new_user->microfinanceBank()->create([
                'micro_finance_bank_id' => $request->microfinance_bank,
            ]);
        }

        $new_user->notify(new AccountCreated($new_user));

        return redirect()->route('manage_users.index', ['role' => $request->role])->with(
            'success',
            'New user created successfully. A registration email has been sent to the user'
        );
    }

    protected function getRolesUserCannotCreate()
    {
        $user = Auth::user();

        //User can create all roles
        if ($user->can('create_super_admin')) {
            return [];
        }

        //User Cannot Create Super Admin
        if ($user->can('create_hod')) {
            return ['super_admin'];
        }

        //User Cannot Create Super_admin and HOD
        if ($user->can('create_admin')) {
            return ['super_admin', 'hod'];
        }

        //User Cannot Create any roles
        return Role::all()->pluck('name')->all();
    }

    protected function getRoles($black_list = [])
    {
        return Role::query()
                   ->whereNotIn('name', $black_list)
                   ->orderBy('name')
                   ->get()
                   ->transform(fn (Role $role) => [
                       'id'   => $role->id,
                       'name' => Str::upper(Str::of($role->name)
                                               ->replace('_', ' ')),
                   ]);
    }

    protected function getUsers($role_id)
    {
        $domain = Auth::user()->domain;

        $role = Role::findById($role_id);

        return User::role($role->id)
                   ->where('domain_id', $domain->id)
                   ->paginate(30)
                   ->transform(fn ($user) => [
                       'id'    => $user->id,
                       'name'  => $user->name,
                       'email' => $user->email,
                       'role'  => Str::upper(Str::of($role->name)
                                                ->replace('_', ' ')),
                   ]);
    }

    protected function getMicrofinanceBanks(Domain $domain)
    {
        return $domain->microFinanceBanks()
                      ->orderBy('name')
                      ->get()
                      ->transform(fn (MicroFinanceBank $mfb) => [
                          'id'   => $mfb->id,
                          'name' => $mfb->name,
                      ]);
    }

    protected function getNewUsers($role_id)
    {
        $domain = Auth::user()->domain;

        $role = Role::findById($role_id);

        return PotentialUser::query()
                            ->where('role_id', $role->id)
                            ->where('domain_id', $domain->id)
                            ->latest()
                            ->get()
                            ->transform(fn ($user) => [
                                'id'    => $user->id,
                                'name'  => $user->name,
                                'email' => $user->email,
                                'email_sent' => optional($user->email_sent)->diffForHumans() ?: 'Not sent',
                                'status' => 'unregistered',
                                'role'  => Str::upper(Str::of($role->name)
                                                         ->replace('_', ' ')),
                            ]);
    }
}
