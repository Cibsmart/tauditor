<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryType;
use App\Models\Domain;
use App\Models\Mda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

use function redirect;

class MdaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $domain = Auth::user()->domain;

        $mdas = Mda::query()
            ->whereIn('beneficiary_type_id', $domain->beneficiaryTypes()->pluck('id'))
            ->withCount('subs')
            ->with('subs:id,mda_id,name')
            ->orderBy('name')
            ->paginate(30)
            ->through(fn (Mda $mda) => [
                'id' => $mda->id,
                'code' => $mda->code,
                'name' => $mda->name,
                'has_sub' => $mda->has_sub,
                'active' => $mda->active,
                'subs_count' => $mda->subs_count,
                'subs' => $mda->subs->map(fn ($sub) => [
                    'id' => $sub->id,
                    'name' => $sub->name,
                ]),
            ]);

        return Inertia::render('Mdas/Index', [
            'can' => [
                'create_mda' => auth()->user()->can('create_mdas'),
            ],
            'mdas' => $mdas,
            'beneficiaryTypes' => $this->getBeneficiaryTypes($domain),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create_mdas'), 403);

        $domain = Auth::user()->domain;

        $request->merge([
            'code' => Str::upper(trim((string) $request->code)),
            'name' => Str::upper(trim((string) $request->name)),
        ]);

        $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('mdas', 'code')],
            'name' => ['required', 'string'],
            'beneficiary_type_id' => [
                'required',
                'string',
                Rule::exists('beneficiary_types', 'id')
                    ->where(fn ($query) => $query->whereIn('id', $domain->beneficiaryTypes()->pluck('id'))),
            ],
            'has_sub' => ['required', 'boolean'],
            'sub_mdas' => ['array', 'required_if:has_sub,true'],
            'sub_mdas.*' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($request) {
            $mda = Mda::create([
                'code' => $request->code,
                'name' => $request->name,
                'beneficiary_type_id' => $request->beneficiary_type_id,
                'has_sub' => $request->has_sub,
                'active' => true,
            ]);

            if ($request->has_sub) {
                foreach ($request->sub_mdas as $name) {
                    $mda->subs()->create([
                        'name' => $name,
                        'active' => true,
                    ]);
                }
            }
        });

        return redirect()->route('mdas.index')->with('success', 'New MDA created successfully.');
    }

    public function update(Request $request, Mda $mda)
    {
        $this->authorizeManage($mda);

        $request->merge([
            'name' => Str::upper(trim((string) $request->name)),
        ]);

        $request->validate([
            'name' => ['required', 'string'],
        ]);

        $mda->update(['name' => $request->name]);

        return redirect()->route('mdas.index')->with('success', 'MDA updated successfully.');
    }

    public function toggleActive(Mda $mda)
    {
        $this->authorizeManage($mda);

        $mda->update(['active' => ! $mda->active]);

        $state = $mda->active ? 'activated' : 'deactivated';

        return redirect()->route('mdas.index')->with('success', "MDA {$state} successfully.");
    }

    public function addSubs(Request $request, Mda $mda)
    {
        $this->authorizeManage($mda);

        $request->validate([
            'sub_mdas' => ['required', 'array', 'min:1'],
            'sub_mdas.*' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($request, $mda) {
            foreach ($request->sub_mdas as $name) {
                $mda->subs()->create([
                    'name' => $name,
                    'active' => true,
                ]);
            }

            if (! $mda->has_sub) {
                $mda->update(['has_sub' => true]);
            }
        });

        return redirect()->route('mdas.index')->with('success', 'Sub-MDAs added successfully.');
    }

    /**
     * Ensure the current user may manage the given MDA: they must hold the
     * create_mdas ability and the MDA must belong to their domain.
     */
    protected function authorizeManage(Mda $mda): void
    {
        abort_unless(Auth::user()->can('create_mdas'), 403);

        $domainTypeIds = Auth::user()->domain->beneficiaryTypes()->pluck('id');

        abort_unless($domainTypeIds->contains($mda->beneficiary_type_id), 403);
    }

    protected function getBeneficiaryTypes(Domain $domain)
    {
        return $domain->beneficiaryTypes()
            ->orderBy('name')
            ->get()
            ->transform(fn (BeneficiaryType $type) => [
                'id' => $type->id,
                'name' => $type->name,
            ]);
    }
}
