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
    $this->seed(CentralRoleSeeder::class);

    $this->tenant = Tenant::create([
        'id' => 'graceusers'.time(),
        'church_name' => 'Grace Chapel',
        'admin_email' => 'pastor@gracechapel.org',
        'admin_name' => 'Pastor John',
    ]);

    $this->withoutMiddleware([
        InitializeTenancyByDomainOrSubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);

    tenancy()->initialize($this->tenant);

    $this->user = User::factory()->create(['is_admin' => true]);
});

test('tenant users index receives a flat paginated payload', function () {
    $this->actingAs($this->user)
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
});
