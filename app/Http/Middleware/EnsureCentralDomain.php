<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class EnsureCentralDomain
{
    public function handle(Request $request, Closure $next)
    {
        $centralDomains = config('tenancy.central_domains', ['church.test', '127.0.0.1', 'localhost']);

        if (app()->environment('testing') && in_array($request->getHost(), ['localhost', '127.0.0.1'], true)) {
            return $next($request);
        }

        if (! in_array($request->getHost(), $centralDomains, true)) {
            abort(404);
        }

        return $next($request);
    }
}
