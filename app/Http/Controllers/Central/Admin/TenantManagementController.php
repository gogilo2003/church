<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\AddCustomDomainRequest;
use App\Http\Requests\Central\TenantRegistrationRequest;
use App\Http\Requests\Central\UpdateTenantRequest;
use App\Services\Central\TenantMigrationService;
use App\Services\Central\TenantProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class TenantManagementController extends Controller
{
    public function __construct(
        private readonly TenantMigrationService $migrationService,
        private readonly TenantProvisioningService $provisioningService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Central/Admin/Tenants/Index', [
            'tenants' => $this->migrationService->getTenantsSummary(),
        ]);
    }

    public function store(TenantRegistrationRequest $request): RedirectResponse
    {
        $dto = $request->toDTO();
        $this->provisioningService->provision($dto);

        return redirect()->route('central.admin.tenants.index')
            ->with('notification', ['success' => 'Tenant provisioned successfully.']);
    }

    public function update(UpdateTenantRequest $request, string $tenant): RedirectResponse
    {
        $this->provisioningService->updateTenantDetails($tenant, $request->validated());

        return redirect()->route('central.admin.tenants.index')
            ->with('notification', ['success' => 'Tenant details updated successfully.']);
    }

    public function addCustomDomain(AddCustomDomainRequest $request): RedirectResponse
    {
        $this->provisioningService->addCustomDomain(
            $request->validated('tenant_id'),
            $request->validated('domain')
        );

        return redirect()->route('central.admin.tenants.index')
            ->with('notification', ['success' => 'Custom domain registered successfully.']);
    }

    public function migrate(Request $request): RedirectResponse
    {
        $tenantId = $request->input('tenant_id');

        if ($tenantId) {
            $this->migrationService->migrateSpecificTenant((string) $tenantId);
            $message = "Database migrations executed for tenant [{$tenantId}].";
        } else {
            $this->migrationService->migrateAllTenants();
            $message = 'Database migrations executed for all tenants.';
        }

        return redirect()->route('central.admin.tenants.index')
            ->with('notification', ['success' => $message]);
    }
}
