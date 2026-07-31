<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\TenantRegistrationRequest;
use App\Models\Central\Domain;
use App\Services\Central\TenantProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class RegisterTenantController extends Controller
{
    public function __construct(
        private readonly TenantProvisioningService $provisioningService,
    ) {}

    public function create(): Response
    {
        return Inertia::render('Central/Auth/RegisterTenant');
    }

    public function checkSubdomain(Request $request): JsonResponse
    {
        $subdomain = strtolower(trim((string) $request->query('subdomain', '')));

        if (empty($subdomain) || ! preg_match('/^[a-z0-9]+$/', $subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomain must be strictly alphanumeric (lowercase letters and numbers).',
            ]);
        }

        $reserved = ['admin', 'app', 'www', 'api', 'central', 'billing', 'support', 'mail', 'system'];
        if (in_array($subdomain, $reserved, true)) {
            return response()->json([
                'available' => false,
                'message' => 'This subdomain is reserved.',
            ]);
        }

        $exists = Domain::where('domain', $subdomain)->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? 'Subdomain is already taken.' : 'Subdomain is available.',
        ]);
    }

    public function store(TenantRegistrationRequest $request): RedirectResponse
    {
        $dto = $request->toDTO();
        $tenant = $this->provisioningService->provision($dto);

        // Redirect user to tenant login or confirmation URL
        $tenantDomain = $tenant->domains()->where('is_primary', true)->first()?->domain ?? $dto->subdomain;
        $appHost = config('app.url', 'http://localhost');
        $scheme = parse_url($appHost, PHP_URL_SCHEME) ?? 'http';
        $baseHost = parse_url($appHost, PHP_URL_HOST) ?? 'church.test';

        $redirectUrl = "{$scheme}://{$tenantDomain}.{$baseHost}/login";

        return Inertia::location($redirectUrl);
    }
}
