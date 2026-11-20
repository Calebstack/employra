<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
            'redirect' => ['nullable', 'string', 'max:2048'],
        ]);

        $email = Str::lower($credentials['email']);
        $guards = [
            'web' => ['email' => $email, 'password' => $credentials['password'], 'status' => 'active'],
            'company' => ['email' => $email, 'password' => $credentials['password'], 'account_status' => 'active'],
            'admin' => ['email' => $email, 'password' => $credentials['password'], 'status' => 'active'],
        ];
        $authenticatedGuard = null;
        foreach ($guards as $guard => $accountCredentials) {
            if (Auth::guard($guard)->attempt($accountCredentials, $request->boolean('remember'))) {
                $authenticatedGuard = $guard;
                Auth::shouldUse($guard);
                break;
            }
        }

        if ($authenticatedGuard === null) {
            throw ValidationException::withMessages([
                'email' => 'These details do not match our records.',
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::guard($authenticatedGuard)->user();
        $user->forceFill(['last_login_at' => now()])->save();

        if ($authenticatedGuard === 'web' && $user->candidateProfile?->onboarding_completed_at === null) {
            return redirect()->route('candidate.onboarding')
                ->with('status', 'Finish setting up your candidate profile.')
                ->with('status_type', 'info');
        }

        $destination = match ($authenticatedGuard) {
            'web' => route('jobs.index'),
            'company' => route('employer.dashboard'),
            'admin' => route('admin.dashboard'),
            default => $this->safeRedirect($credentials['redirect'] ?? null),
        };

        return redirect($destination)
            ->with('status', 'You are now signed in.')
            ->with('status_type', 'success');
    }

    private function safeRedirect(?string $redirect): string
    {
        if ($redirect !== null && Str::startsWith($redirect, '/') && ! Str::startsWith($redirect, '//') && ! Str::startsWith($redirect, '/\\')) {
            return $redirect;
        }

        return route('home');
    }
}
