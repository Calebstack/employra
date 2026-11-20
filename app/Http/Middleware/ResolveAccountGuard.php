<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResolveAccountGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        foreach ($request->route()?->gatherMiddleware() ?? [] as $middleware) {
            if (in_array($middleware, ['auth:web', 'auth:company', 'auth:admin'], true)) {
                Auth::shouldUse(substr($middleware, strlen('auth:')));

                return $next($request);
            }
        }

        foreach (['web', 'company', 'admin'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);

                return $next($request);
            }
        }

        Auth::shouldUse('web');

        return $next($request);
    }
}
