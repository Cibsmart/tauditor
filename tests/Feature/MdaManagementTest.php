<?php

use App\Models\BeneficiaryType;
use App\Models\Domain;
use App\Models\Mda;
use App\Models\SubMda;
use Tests\Feature\Actions\AutopayTestSetup;

uses(AutopayTestSetup::class);

function mdaManagerWithDomain(object $test): array
{
    $domain = $test->createDomain();
    $user = $test->createUser($domain);
    $user->givePermissionTo('view_mdas', 'create_mdas');
    $beneficiaryType = $test->createBeneficiaryType($domain);

    return compact('domain', 'user', 'beneficiaryType');
}

it('creates an MDA without sub-MDAs', function () {
    ['user' => $user, 'beneficiaryType' => $beneficiaryType] = mdaManagerWithDomain($this);

    $response = $this->actingAs($user)->post(route('mdas.store'), [
        'code' => 'edu',
        'name' => 'Ministry of Education',
        'beneficiary_type_id' => $beneficiaryType->id,
        'has_sub' => false,
        'sub_mdas' => [],
    ]);

    $response->assertRedirect(route('mdas.index'));
    expect(session('success'))->toContain('created');

    $mda = Mda::where('code', 'EDU')->first();
    expect($mda)->not->toBeNull();
    expect($mda->name)->toBe('Ministry of Education');
    expect($mda->has_sub)->toBeFalse();
    expect($mda->active)->toBeTrue();
    expect($mda->subs()->count())->toBe(0);
});

it('creates an MDA with its sub-MDAs in one submit', function () {
    ['user' => $user, 'beneficiaryType' => $beneficiaryType] = mdaManagerWithDomain($this);

    $response = $this->actingAs($user)->post(route('mdas.store'), [
        'code' => 'health',
        'name' => 'Ministry of Health',
        'beneficiary_type_id' => $beneficiaryType->id,
        'has_sub' => true,
        'sub_mdas' => ['Primary Healthcare', 'Hospital Management Board'],
    ]);

    $response->assertRedirect(route('mdas.index'));

    $mda = Mda::where('code', 'HEALTH')->first();
    expect($mda->has_sub)->toBeTrue();

    $subs = SubMda::where('mda_id', $mda->id)->pluck('name')->all();
    expect($subs)->toHaveCount(2);
    expect($subs)->toContain('Primary Healthcare', 'Hospital Management Board');
});

it('fails validation when has_sub is true but no sub-MDAs are given', function () {
    ['user' => $user, 'beneficiaryType' => $beneficiaryType] = mdaManagerWithDomain($this);

    $response = $this->actingAs($user)->post(route('mdas.store'), [
        'code' => 'works',
        'name' => 'Ministry of Works',
        'beneficiary_type_id' => $beneficiaryType->id,
        'has_sub' => true,
        'sub_mdas' => [],
    ]);

    $response->assertSessionHasErrors('sub_mdas');
    expect(Mda::where('code', 'WORKS')->exists())->toBeFalse();
});

it('fails validation when code or name is missing', function () {
    ['user' => $user, 'beneficiaryType' => $beneficiaryType] = mdaManagerWithDomain($this);

    $response = $this->actingAs($user)->post(route('mdas.store'), [
        'code' => '',
        'name' => '',
        'beneficiary_type_id' => $beneficiaryType->id,
        'has_sub' => false,
    ]);

    $response->assertSessionHasErrors(['code', 'name']);
});

it('rejects a beneficiary type from another domain', function () {
    ['user' => $user] = mdaManagerWithDomain($this);

    $otherDomain = Domain::create(['id' => 'other-domain', 'name' => 'Other Domain']);
    // Created directly (not via the helper, which hard-codes id 'bt-test') so it
    // belongs to a different domain than the acting user's.
    BeneficiaryType::create([
        'id' => 'bt-other',
        'name' => 'Staff',
        'domain_id' => $otherDomain->id,
    ]);

    $response = $this->actingAs($user)->post(route('mdas.store'), [
        'code' => 'spy',
        'name' => 'Cross Domain MDA',
        'beneficiary_type_id' => 'bt-other',
        'has_sub' => false,
    ]);

    $response->assertSessionHasErrors('beneficiary_type_id');
    expect(Mda::where('code', 'SPY')->exists())->toBeFalse();
});

it('forbids a user without view_mdas from listing MDAs', function () {
    $domain = $this->createDomain();
    $user = $this->createUser($domain);

    $this->actingAs($user)->get(route('mdas.index'))->assertForbidden();
});

it('forbids a user with only view_mdas from storing an MDA', function () {
    ['domain' => $domain, 'beneficiaryType' => $beneficiaryType] = mdaManagerWithDomain($this);

    $viewer = $this->createUser($domain);
    $viewer->givePermissionTo('view_mdas');

    $response = $this->actingAs($viewer)->post(route('mdas.store'), [
        'code' => 'noaccess',
        'name' => 'No Access MDA',
        'beneficiary_type_id' => $beneficiaryType->id,
        'has_sub' => false,
    ]);

    $response->assertForbidden();
    expect(Mda::where('code', 'NOACCESS')->exists())->toBeFalse();
});
