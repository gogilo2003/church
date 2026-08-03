<?php

declare(strict_types=1);

namespace App\Services\Central;

use App\DataTransferObjects\OnboardingData;
use App\Models\Central\Tenant;
use Illuminate\Support\Facades\DB;

final class TenantProvisioningService
{
    /**
     * Provision a new tenant database and domain record.
     */
    public function provision(OnboardingData $data): Tenant
    {
        $connection = config('tenancy.database.central_connection') ?? config('database.default');

        return DB::connection($connection)->transaction(function () use ($data) {
            $tenant = Tenant::create([
                'id' => $data->subdomain,
                'data' => [
                    'church_name' => $data->churchName,
                    'admin_name' => $data->adminName,
                    'admin_email' => $data->adminEmail,
                    'admin_password' => bcrypt($data->adminPassword),
                    'phone' => $data->phone,
                    'is_central_admin_created' => $data->isCentralAdminCreated,
                ],
            ]);

            // Register internal subdomain (storing ONLY the alphanumeric string, e.g. "abc")
            $tenant->domains()->create([
                'domain' => $data->subdomain,
                'is_primary' => true,
            ]);

            return $tenant;
        });
    }

    /**
     * Add an external custom domain to an existing tenant (e.g. mis.elck.org or churchabc.com).
     */
    public function addCustomDomain(string $tenantId, string $customDomain): mixed
    {
        $tenant = Tenant::findOrFail($tenantId);

        return $tenant->domains()->create([
            'domain' => strtolower(trim($customDomain)),
            'is_primary' => false,
        ]);
    }

    /**
     * Update existing tenant configuration details.
     */
    public function updateTenantDetails(string $tenantId, array $data): Tenant
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenantData = $tenant->data ?? [];

        if (isset($data['church_name'])) {
            $tenantData['church_name'] = trim($data['church_name']);
        }
        if (isset($data['admin_name'])) {
            $tenantData['admin_name'] = trim($data['admin_name']);
        }
        if (isset($data['admin_email'])) {
            $tenantData['admin_email'] = trim($data['admin_email']);
        }
        if (isset($data['phone'])) {
            $tenantData['phone'] = trim($data['phone']);
        }

        $tenant->data = $tenantData;
        $tenant->save();

        return $tenant;
    }
}
