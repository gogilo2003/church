<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralUser
{
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('central.admin.login');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (! empty($allowedRoles)) {
            if (! in_array($user->role, $allowedRoles, true)) {
                abort(403, 'Unauthorized central role access.');
            }
        }

        return $next($request);
    }
}
