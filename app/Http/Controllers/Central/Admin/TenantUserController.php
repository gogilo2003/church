<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\ResetTenantUserPasswordRequest;
use App\Http\Requests\Central\StoreTenantUserRequest;
use App\Http\Requests\Central\ToggleTenantUserStatusRequest;
use App\Http\Requests\Central\UpdateTenantUserRequest;
use App\Models\Central\Tenant;
use App\Services\Central\TenantUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class TenantUserController extends Controller
{
    public function __construct(
        private readonly TenantUserService $userService
    ) {}

    public function index(Request $request, string $tenantId): Response
    {
        $tenant = Tenant::findOrFail($tenantId);
        $filters = $request->only(['search', 'status', 'sort_by', 'sort_order']);
        $pageData = $this->userService->getUsersPageData($tenant, $filters);

        return Inertia::render('Central/Admin/Tenants/Users', [
            'tenant' => $this->tenantSummary($tenant),
            'users' => $pageData['users'],
            'roles' => $pageData['roles'],
            'filters' => $filters,
        ]);
    }

    public function store(StoreTenantUserRequest $request, string $tenantId): RedirectResponse
    {
        $this->userService->createUser(Tenant::findOrFail($tenantId), $request->validated());

        return back()->with('notification', ['success' => 'Tenant user created successfully.']);
    }

    public function update(UpdateTenantUserRequest $request, string $tenantId, string $user): RedirectResponse
    {
        $this->userService->updateUser(Tenant::findOrFail($tenantId), (int) $user, $request->validated());

        return back()->with('notification', ['success' => 'Tenant user updated successfully.']);
    }

    public function toggleStatus(ToggleTenantUserStatusRequest $request, string $tenantId, string $user): RedirectResponse
    {
        $this->userService->changeStatus(Tenant::findOrFail($tenantId), (int) $user, $request->validated('status'));

        return back()->with('notification', ['success' => 'Tenant user status updated.']);
    }

    public function resetPassword(ResetTenantUserPasswordRequest $request, string $tenantId, string $user): RedirectResponse
    {
        $this->userService->resetPassword(Tenant::findOrFail($tenantId), (int) $user, $request->validated('password'));

        return back()->with('notification', ['success' => 'Tenant user password reset successfully.']);
    }

    private function tenantSummary(Tenant $tenant): array
    {
        return [
            'id' => $tenant->id,
            'church_name' => $tenant->church_name ?? $tenant->id,
            'subdomain' => $tenant->domains->where('is_primary', true)->first()?->domain ?? $tenant->id,
        ];
    }
}
