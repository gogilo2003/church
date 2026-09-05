<?php

declare(strict_types=1);

namespace App\Services\Central;

use App\Models\Central\Tenant;
use Illuminate\Support\Facades\Artisan;

final class TenantMigrationService
{
    /**
     * Run tenant database migrations for all tenants.
     */
    public function migrateAllTenants(): int
    {
        return Artisan::call('tenants:migrate');
    }

    /**
     * Run tenant database migrations for a specific tenant.
     */
    public function migrateSpecificTenant(string $tenantId): int
    {
        $tenant = Tenant::findOrFail($tenantId);

        return Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id],
        ]);
    }

    /**
     * Get migration status across tenants.
     */
    public function getTenantsSummary(): array
    {
        $tenants = Tenant::with('domains')->latest()->get();

        return $tenants->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'church_name' => $tenant->church_name ?? $tenant->id,
                'admin_name' => $tenant->admin_name ?? null,
                'admin_email' => $tenant->admin_email ?? null,
                'phone' => $tenant->phone ?? null,
                'subdomain' => $tenant->domains->where('is_primary', true)->first()?->domain ?? $tenant->id,
                'custom_domains' => $tenant->domains->where('is_primary', false)->pluck('domain')->all(),
                'created_at' => $tenant->created_at?->toIso8601String(),
            ];
        })->all();
    }
}
