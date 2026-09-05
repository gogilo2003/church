<?php

declare(strict_types=1);

use App\Models\Central\Tenant;
use App\Models\User;
use Database\Seeders\CentralRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

uses(RefreshDatabase::class);

beforeEach(function () {
    static $sharedTenant = null;

    $this->seed(CentralRoleSeeder::class);

    if ($sharedTenant === null) {
        $sharedTenant = Tenant::create([
            'id' => 'graceusers'.time(),
            'church_name' => 'Grace Chapel',
            'admin_email' => 'pastor@gracechapel.org',
            'admin_name' => 'Pastor John',
        ]);
    }

    $this->tenant = $sharedTenant;

    $this->withoutMiddleware([
        InitializeTenancyByDomainOrSubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

test('tenant users and roles index pages receive flat paginated payloads', function () {
    $user = $this->tenant->run(fn () => User::factory()->create(['is_admin' => true]));

    tenancy()->initialize($this->tenant);

    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(function ($page) {
            $page->component('Users/Index');

            $users = $page->toArray()['props']['users'];

            expect($users)->toHaveKeys(['data', 'current_page', 'last_page', 'per_page', 'total', 'links'])
                ->and($users['data'])->toBeArray()
                ->and($users['links'])->toBeArray()
                ->and($users['links'][0])->toHaveKeys(['url', 'label', 'active'])
                ->and(array_key_exists('meta', $users))->toBeFalse();
        });

    $this->actingAs($user)
        ->get(route('roles.index'))
        ->assertOk()
        ->assertInertia(function ($page) {
            $page->component('Roles/Index');

            $roles = $page->toArray()['props']['roles'];

            expect($roles)->toHaveKeys(['data', 'current_page', 'last_page', 'per_page', 'total', 'links'])
                ->and($roles['data'])->toBeArray()
                ->and($roles['links'])->toBeArray()
                ->and($roles['links'][0])->toHaveKeys(['url', 'label', 'active'])
                ->and(array_key_exists('meta', $roles))->toBeFalse();
        });
});