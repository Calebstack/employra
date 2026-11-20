<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobInterview;
use App\Services\JobApplicationWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployerApplicationController extends Controller
{
    public function updateStatus(Request $request, int $id, JobApplicationWorkflow $workflow): RedirectResponse
    {
        $company = $this->company($request);
        $application = $this->application($company, $id);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['reviewing', 'shortlisted', 'interview', 'offer'])],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $workflow->transition($application, $validated['status'], 'company', $company->id, $validated['message'] ?? null);

        return back()->with('status', 'Application status updated.');
    }

    public function decline(Request $request, int $id, JobApplicationWorkflow $workflow): RedirectResponse
    {
        $company = $this->company($request);
        $application = $this->application($company, $id);
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $workflow->transition(
            $application,
            'rejected',
            'company',
            $company->id,
            $validated['message'] ?? 'The company is not moving forward with this application.'
        );

        return redirect()->route('employer.applicants.show', $application)
            ->with('status', 'The application was declined.');
    }

    public function scheduleInterview(Request $request, int $id, JobApplicationWorkflow $workflow): RedirectResponse
    {
        $company = $this->company($request);
        $application = $this->application($company, $id);
        abort_unless(in_array($application->status, ['submitted', 'reviewing', 'shortlisted', 'interview'], true), 409);

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'format' => ['required', Rule::in(['video', 'phone', 'in_person'])],
            'meeting_details' => [Rule::requiredIf($request->input('format') !== 'phone'), 'nullable', 'string', 'max:2000'],
            'candidate_message' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($application, $company, $validated, $workflow): void {
            JobInterview::where('job_application_id', $application->id)
                ->where('status', 'scheduled')
                ->update(['status' => 'rescheduled']);
            JobInterview::create([
                'job_application_id' => $application->id,
                'scheduled_at' => $validated['scheduled_at'],
                'format' => $validated['format'],
                'meeting_details' => $validated['meeting_details'] ?? null,
                'candidate_message' => $validated['candidate_message'] ?? null,
                'status' => 'scheduled',
                'candidate_response' => 'pending',
            ]);

            if ($application->status !== 'interview') {
                $workflow->transition(
                    $application,
                    'interview',
                    'company',
                    $company->id,
                    'An interview was scheduled.'
                );
            }
        });

        return back()->with('status', 'Interview scheduled and shared with the candidate.');
    }

    public function downloadCv(Request $request, int $id): StreamedResponse
    {
        $application = $this->application($this->company($request), $id);
        $cv = $application->cv;
        abort_unless($cv !== null, 404);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($cv->stored_path), 404);

        return $disk->download($cv->stored_path, $cv->original_name, [
            'Content-Type' => $cv->mime,
        ]);
    }

    private function company(Request $request): Company
    {
        $company = $request->attributes->get('employerCompany');
        abort_unless($company instanceof Company, 403);

        return $company;
    }

    private function application(Company $company, int $id): JobApplication
    {
        return JobApplication::whereKey($id)
            ->whereHas('job', fn ($jobs) => $jobs->where('company_id', $company->id))
            ->firstOrFail();
    }
}
