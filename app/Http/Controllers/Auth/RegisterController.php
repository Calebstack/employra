<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\User;
use App\Support\PlatformSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request): RedirectResponse
    {
        abort_unless(PlatformSettings::get('allow_registration', true), 403, 'New account registration is currently closed.');

        $data = $request->validated();

        [$account, $guard] = DB::transaction(function () use ($data) {
            if ($data['role'] === 'candidate') {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => Str::lower($data['email']),
                    'password' => $data['password'],
                    'role' => 'candidate',
                    'status' => 'active',
                ]);
                CandidateProfile::create(['user_id' => $user->id]);

                return [$user, 'web'];
            }

            $company = Company::create([
                    'name' => $data['company_name'],
                    'slug' => Str::slug($data['company_name']).'-'.Str::lower(Str::random(6)),
                    'industry' => $data['industry'],
                    'status' => 'pending',
                    'contact_name' => $data['name'],
                    'email' => Str::lower($data['email']),
                    'password' => $data['password'],
                    'account_status' => 'active',
                ]);

            return [$company, 'company'];
        });

        Auth::guard($guard)->login($account);
        $request->session()->regenerate();

        $destination = $guard === 'web'
            ? route('candidate.onboarding')
            : route('employer.dashboard');

        if ($guard === 'web') {
            return redirect($destination);
        }

        return redirect($destination)
            ->with('status', 'Your account has been created.')
            ->with('status_type', 'success');
    }

}
