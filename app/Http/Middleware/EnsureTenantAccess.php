<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to enforce tenant-level granular permissions.
 *
 * Usage in routes:
 *   ->middleware(EnsureTenantAccess::class . ':members.read')
 *   ->middleware(EnsureTenantAccess::class . ':tithes.write,offerings.write')
 *
 * Tenant Super-Users (is_admin = true) bypass all permission checks.
 */
class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next, string ...$requiredPermissions): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Tenant Super-Users bypass all permission checks
        if ($user->is_admin) {
            return $next($request);
        }

        // If no specific permissions required, just check auth
        if (empty($requiredPermissions)) {
            return $next($request);
        }

        // Check if user has at least one of the required permissions
        foreach ($requiredPermissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}
