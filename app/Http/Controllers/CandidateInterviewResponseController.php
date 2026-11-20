<?php

namespace App\Http\Controllers;

use App\Models\JobInterview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CandidateInterviewResponseController extends Controller
{
    public function update(Request $request, int $id): RedirectResponse
    {
        $candidate = $request->user();
        abort_unless($candidate?->role === 'candidate', 403);
        $interview = JobInterview::whereKey($id)
            ->whereHas('application', fn ($applications) => $applications->where('user_id', $candidate->id))
            ->firstOrFail();
        abort_unless($interview->status === 'scheduled' && $interview->scheduled_at->isFuture(), 409);

        $validated = $request->validate([
            'response' => ['required', Rule::in(['confirmed', 'reschedule_requested'])],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $interview->forceFill([
            'candidate_response' => $validated['response'],
            'response_note' => $validated['note'] ?? null,
            'responded_at' => now(),
        ])->save();

        return redirect()->route('candidate.applications.show', $interview->job_application_id)
            ->with('status', $validated['response'] === 'confirmed'
                ? 'Your attendance has been confirmed.'
                : 'Your reschedule request has been sent to the company.');
    }
}
