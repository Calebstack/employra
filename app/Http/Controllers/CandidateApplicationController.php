<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Services\JobApplicationWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CandidateApplicationController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $candidate = $request->user();
        abort_unless($candidate?->role === 'candidate', 403);

        $job = Job::query()
            ->where('slug', $slug)
            ->where('status', 'open')
            ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
            ->firstOrFail();

        $profile = $candidate->candidateProfile()->firstOrCreate([]);
        $validated = $request->validate([
            'cv_id' => [
                'nullable',
                'integer',
                Rule::exists('cvs', 'id')
                    ->where('candidate_profile_id', $profile->id)
                    ->whereNull('deleted_at'),
            ],
            'cover_note' => ['nullable', 'string', 'max:1000'],
            'consent' => ['required', 'accepted'],
        ]);

        if (JobApplication::where('user_id', $candidate->id)->where('job_id', $job->id)->exists()) {
            return redirect()->route('candidate.applications')
                ->with('status', 'You have already applied for this job.');
        }

        DB::transaction(function () use ($candidate, $job, $profile, $validated): void {
            $application = JobApplication::create([
                'user_id' => $candidate->id,
                'job_id' => $job->id,
                'cv_id' => $validated['cv_id'] ?? null,
                'cover_note' => $validated['cover_note'] ?? null,
                'availability' => $profile->availability,
                'expected_salary_min' => $profile->expected_salary_min,
                'expected_salary_max' => $profile->expected_salary_max,
            ]);

            app(JobApplicationWorkflow::class)->recordSubmission($application, $candidate->id);
        });

        return redirect()->route('candidate.applications')
            ->with('status', 'Your application has been submitted.');
    }

    public function withdraw(Request $request, int $id): RedirectResponse
    {
        $candidate = $request->user();
        abort_unless($candidate?->role === 'candidate', 403);
        $application = JobApplication::where('user_id', $candidate->id)->findOrFail($id);

        app(JobApplicationWorkflow::class)->transition(
            $application,
            'withdrawn',
            'candidate',
            $candidate->id,
            'Candidate withdrew this application.'
        );

        return redirect()->route('candidate.applications.show', $application)
            ->with('status', 'Your application has been withdrawn.');
    }
}
