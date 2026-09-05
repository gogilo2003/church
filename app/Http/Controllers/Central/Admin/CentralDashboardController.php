<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Central\Domain;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CentralDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $totalTenants = Tenant::count();
        $totalDomains = Domain::count();
        $customDomainsCount = Domain::where('is_primary', false)->count();

        $recentTenants = Tenant::with('domains')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($tenant) => [
                'id' => $tenant->id,
                'church_name' => $tenant->church_name ?? $tenant->id,
                'admin_email' => $tenant->admin_email ?? null,
                'subdomain' => $tenant->domains->where('is_primary', true)->first()?->domain ?? $tenant->id,
                'custom_domains' => $tenant->domains->where('is_primary', false)->pluck('domain')->all(),
                'created_at' => $tenant->created_at?->diffForHumans() ?? 'Recently',
            ]);

        $stats = [
            [
                'name' => 'Total Church Workspaces',
                'value' => $totalTenants,
                'icon' => 'building',
                'color' => 'blue',
            ],
            [
                'name' => 'Active Subdomains',
                'value' => $totalDomains - $customDomainsCount,
                'icon' => 'globe',
                'color' => 'indigo',
            ],
            [
                'name' => 'Custom External Domains',
                'value' => $customDomainsCount,
                'icon' => 'link',
                'color' => 'green',
            ],
            [
                'name' => 'System Health',
                'value' => 'Operational',
                'icon' => 'check-circle',
                'color' => 'emerald',
            ],
        ];

        return Inertia::render('Central/Admin/Dashboard', [
            'stats' => $stats,
            'recentTenants' => $recentTenants,
        ]);
    }
}
