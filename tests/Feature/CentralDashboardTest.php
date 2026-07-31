<?php

declare(strict_types=1);

use App\Models\Central\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated admin user can view central platform dashboard', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $tenant = Tenant::create(['id' => 'grace']);
    $tenant->domains()->create(['domain' => 'grace', 'is_primary' => true]);

    $response = $this->actingAs($admin)
        ->get(route('central.admin.dashboard'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Central/Admin/Dashboard')
            ->has('stats', 4)
            ->has('recentTenants', 1)
        );
});
