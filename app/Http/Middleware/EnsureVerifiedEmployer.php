<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Company;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerifiedEmployer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user?->role === 'employer', 403);

        abort_unless($user instanceof Company, 403);
        $company = $user;

        if ($company->status !== 'verified') {
            return redirect()->route('employer.dashboard');
        }

        $request->attributes->set('employerCompany', $company);

        return $next($request);
    }
}
