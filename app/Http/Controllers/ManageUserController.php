<?php

namespace App\Http\Controllers;

use App\User;
use App\ValueType;
use Inertia\Inertia;
use App\AllowanceType;
use App\AllowanceName;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use function redirect;
use function in_array;

class ManageUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $users = (object) [];

        $roles = Role::query()
                     ->orderBy('name')
                     ->get()
                     ->transform(fn (Role $role) => [
                         'id'   => $role->id,
                         'name' => Str::upper(Str::of($role->name)
                                                 ->replace('_', ' ')),
                     ]);

        if ($request->has('role')) {

            $domain = Auth::user()->domain;

            $role = Role::findById($request->role);

            $users = User::role($role->id)
                         ->where('domain_id', $domain->id)
                         ->paginate(30)
                         ->transform(fn ($user) => [
                             'id' => $user->id,
                             'name' => $user->name,
                             'email' => $user->email,
                             'role' => Str::upper(Str::of($role->name)
                                                     ->replace('_', ' ')),
                         ]);
        }

        return Inertia::render('ManageUsers/Index', [
            'roles' => $roles,
            'users' => $users,
        ]);
    }

    public function create()
    {
        $value_types = ValueType::all();
        $allowance_types = AllowanceType::all();
        $allowance_names = AllowanceName::all();

        return Inertia::render('Allowances/Create', [
            'value_types'     => $value_types,
            'allowance_types' => $allowance_types,
            'allowance_names' => $allowance_names,
        ]);
    }

    public function store(Request $request)
    {
        $allowance_name = $this->allowanceName(
            $request->allowance_type,
            $request->allowance_name,
            $request->new_allowance
        );

        $valuable = $this->valueType($request->value_type, $request->value, $allowance_name->name);

        $valuable->allowance()->create([
            'allowance_name_id' => $allowance_name->id,
            'domain_id'         => Auth::user()->domain->id,
        ]);

        return redirect()->back()->with('success', 'allowance Created Successfully');
    }
}
