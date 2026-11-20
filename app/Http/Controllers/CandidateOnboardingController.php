<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CandidateOnboardingRequest;
use App\Models\CandidateProfile;
use App\Services\PrivateCvStorage;
use App\Support\CandidateProfilePresenter;
use App\Support\MockData;
use App\Support\NigerianStates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CandidateOnboardingController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        abort_unless($request->user()?->role === 'candidate', 403);

        $profile = $request->user()->candidateProfile()->firstOrCreate([]);
        if ($profile->onboarding_completed_at !== null) {
            return redirect()->route('jobs.index');
        }

        return view('candidate.onboarding', [
            'profile' => $profile,
            'roleSuggestions' => array_values(array_unique(array_column(MockData::jobs(), 'title'))),
            'stateOptions' => NigerianStates::options(),
            'preferredLocations' => array_values(array_filter(array_map(
                static fn ($location) => is_string($location) ? NigerianStates::normalize($location) : null,
                $profile->preferred_locations ?? [],
            ), 'is_string')),
            'currentCv' => $profile->cvs()->orderByDesc('is_primary')->latest()->first(),
        ]);
    }

    public function store(CandidateOnboardingRequest $request, PrivateCvStorage $cvStorage): RedirectResponse
    {
        $data = $request->validated();
        $profile = $request->user()->candidateProfile()->firstOrCreate([]);

        if (isset($data['cv'])) {
            $cvStorage->store($profile, $data['cv']);
        }

        $roles = array_map('trim', $data['preferred_roles']);
        $workModes = array_values($data['preferred_work_modes']);
        $locations = array_map('trim', $data['preferred_locations']);

        DB::transaction(function () use ($profile, $data, $roles, $workModes, $locations): void {
            $profile->fill([
                'phone' => $data['phone'] ?? null,
                'preferred_roles' => $roles,
                'preferred_work_modes' => $workModes,
                'preferred_locations' => $locations,
                'expected_salary_min' => (int) $data['expected_salary_min'],
                'expected_salary_max' => (int) $data['expected_salary_max'],
                'availability' => $data['availability'],
                'onboarding_completed_at' => now(),
            ]);
            $profile->completion = CandidateProfilePresenter::completion($profile);
            $profile->save();
        });

        return redirect()->route('jobs.index')
            ->with('status', 'Your profile preferences have been saved.')
            ->with('status_type', 'success');
    }
}
