<?php

declare(strict_types=1);

use App\Models\Central\Tenant;
use App\Models\User;
use App\Services\Central\TenantMigrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

test('central admin can view tenants summary and domain mappings', function () {
    $tenant = Tenant::create(['id' => 'elck']);
    $tenant->domains()->create(['domain' => 'elck', 'is_primary' => true]);
    $tenant->domains()->create(['domain' => 'mis.elck.org', 'is_primary' => false]);

    $service = new TenantMigrationService;
    $summary = $service->getTenantsSummary();

    expect($summary)->toHaveCount(1);
    expect($summary[0]['subdomain'])->toBe('elck');
    expect($summary[0]['custom_domains'])->toContain('mis.elck.org');
});

test('central admin can trigger tenant database migrations via controller', function () {
    Artisan::shouldReceive('call')
        ->with('tenants:migrate')
        ->once()
        ->andReturn(0);

    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)
        ->post(route('central.admin.tenants.migrate'), []);

    $response->assertRedirect();
});
