<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Cv;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobInterview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_company_and_admin_can_follow_a_persisted_application_to_interview(): void
    {
        Storage::fake('local');
        $company = $this->company();
        $job = $this->job($company);
        $candidate = User::factory()->create(['role' => 'candidate', 'name' => 'Lifecycle Candidate']);
        $profile = CandidateProfile::create([
            'user_id' => $candidate->id,
            'availability' => 'within_2_weeks',
            'expected_salary_min' => 300000,
            'expected_salary_max' => 500000,
        ]);
        $photoPath = 'candidate-profile-photos/'.$profile->id.'/profile.png';
        Storage::disk('local')->put($photoPath, $this->validPng());
        $profile->forceFill(['photo_path' => $photoPath])->save();
        Storage::disk('local')->put('cvs/'.$profile->id.'/resume.pdf', "%PDF-1.4\nResume test");
        $cv = Cv::create([
            'candidate_profile_id' => $profile->id,
            'original_name' => 'resume.pdf',
            'stored_path' => 'cvs/'.$profile->id.'/resume.pdf',
            'mime' => 'application/pdf',
            'size_bytes' => 24,
            'sha256' => hash('sha256', "%PDF-1.4\nResume test"),
            'status' => 'uploaded',
            'is_primary' => true,
        ]);

        $this->actingAs($candidate)->post(route('jobs.apply.submit', $job->slug), [
            'cv_id' => $cv->id,
            'cover_note' => 'I have relevant Laravel experience.',
            'consent' => '1',
        ])->assertRedirect(route('candidate.applications'));

        $application = JobApplication::where('user_id', $candidate->id)->firstOrFail();
        $this->assertSame('within_2_weeks', $application->availability);
        $this->assertSame(300000, $application->expected_salary_min);
        $this->assertDatabaseHas('job_application_status_histories', [
            'job_application_id' => $application->id,
            'status' => 'submitted',
            'actor_type' => 'candidate',
            'actor_id' => $candidate->id,
        ]);

        $this->actingAs($company, 'company')
            ->get(route('employer.applicants.show', $application))
            ->assertOk()
            ->assertSee('resume.pdf')
            ->assertSee('Download submitted CV')
            ->assertSee('Available within 2 weeks')
            ->assertSee('applicantProfilePhotoPreview');
        $this->get(route('employer.applicants.photo', $application))->assertOk();
        $this->get(route('employer.applicants.cv', $application))
            ->assertOk()
            ->assertDownload('resume.pdf');

        $this->post(route('employer.applicants.status', $application), [
            'status' => 'shortlisted',
            'message' => 'Your experience is a strong match.',
        ])->assertRedirect();
        $this->assertDatabaseHas('job_application_status_histories', [
            'job_application_id' => $application->id,
            'status' => 'shortlisted',
            'actor_type' => 'company',
            'actor_id' => $company->id,
            'message' => 'Your experience is a strong match.',
        ]);

        $scheduledAt = now()->addDays(2)->setTime(10, 30)->format('Y-m-d\TH:i');
        $this->post(route('employer.applicants.interviews.store', $application), [
            'scheduled_at' => $scheduledAt,
            'format' => 'video',
            'meeting_details' => 'https://meet.example/interview',
            'candidate_message' => 'Please join five minutes early.',
        ])->assertRedirect();
        $interview = JobInterview::where('job_application_id', $application->id)->firstOrFail();
        $this->assertSame('interview', $application->fresh()->status);
        $this->assertDatabaseHas('job_application_status_histories', [
            'job_application_id' => $application->id,
            'status' => 'interview',
            'actor_type' => 'company',
        ]);

        $this->actingAs($candidate, 'web')
            ->get(route('candidate.applications.show', $application))
            ->assertOk()
            ->assertSee('Interview')
            ->assertSee('Please join five minutes early.')
            ->assertSee('Confirm attendance');
        $this->post(route('candidate.interviews.response', $interview), [
            'response' => 'reschedule_requested',
            'note' => 'I have a conflict at that time.',
        ])->assertRedirect(route('candidate.applications.show', $application));
        $this->assertDatabaseHas('job_interviews', [
            'id' => $interview->id,
            'candidate_response' => 'reschedule_requested',
        ]);

        $this->actingAs($company, 'company')
            ->post(route('employer.applicants.interviews.store', $application), [
                'scheduled_at' => now()->addDays(3)->setTime(11, 0)->format('Y-m-d\TH:i'),
                'format' => 'phone',
            ])->assertRedirect();
        $this->assertDatabaseHas('job_interviews', [
            'id' => $interview->id,
            'status' => 'rescheduled',
        ]);
        $newInterview = JobInterview::where('job_application_id', $application->id)
            ->where('status', 'scheduled')
            ->firstOrFail();

        $this->actingAs($candidate, 'web')->get(route('candidate.applications.show', $application))
            ->assertOk()
            ->assertSee('Phone call');
        $this->post(route('candidate.interviews.response', $newInterview), [
            'response' => 'confirmed',
        ])->assertRedirect(route('candidate.applications.show', $application));
        $this->assertDatabaseHas('job_interviews', [
            'id' => $newInterview->id,
            'candidate_response' => 'confirmed',
        ]);

        $admin = Admin::create([
            'name' => 'Lifecycle Admin',
            'email' => 'lifecycle-admin@example.test',
            'password' => 'long-test-password',
        ]);
        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.show', $candidate))
            ->assertOk()
            ->assertSee('Status history')
            ->assertSee('Your experience is a strong match.')
            ->assertSee('Interviews')
            ->assertSee('confirmed')
            ->assertSee('adminCandidatePhotoPreview');
        $this->get(route('admin.users.photo', $candidate))->assertOk();
    }

    public function test_employer_cannot_access_another_companys_application_or_cv(): void
    {
        Storage::fake('local');
        $owner = $this->company();
        $otherCompany = $this->company('Other Company', 'other-company');
        $job = $this->job($owner);
        $candidate = User::factory()->create(['role' => 'candidate']);
        $profile = CandidateProfile::create(['user_id' => $candidate->id]);
        Storage::disk('local')->put('private/resume.pdf', 'private');
        $cv = Cv::create([
            'candidate_profile_id' => $profile->id,
            'original_name' => 'resume.pdf',
            'stored_path' => 'private/resume.pdf',
            'mime' => 'application/pdf',
            'size_bytes' => 7,
            'sha256' => hash('sha256', 'private'),
            'status' => 'uploaded',
        ]);
        $application = JobApplication::create([
            'user_id' => $candidate->id,
            'job_id' => $job->id,
            'cv_id' => $cv->id,
        ]);

        $this->actingAs($otherCompany, 'company')
            ->get(route('employer.applicants.show', $application))
            ->assertNotFound();
        $this->get(route('employer.applicants.cv', $application))->assertNotFound();
    }

    public function test_company_decline_and_candidate_withdrawal_are_recorded_in_history(): void
    {
        $company = $this->company();
        $job = $this->job($company);
        $candidates = collect([
        User::factory()->create(['role' => 'candidate']),
        User::factory()->create(['role' => 'candidate']),
        ]);
        $applications = $candidates->map(function (User $candidate) use ($job): JobApplication {
        CandidateProfile::create(['user_id' => $candidate->id]);
        $this->actingAs($candidate, 'web')->post(route('jobs.apply.submit', $job->slug), [
            'consent' => '1',
        ])->assertRedirect(route('candidate.applications'));

        return JobApplication::where('user_id', $candidate->id)->firstOrFail();
        });

        $declined = $applications->first();
        $this->actingAs($company, 'company')
        ->post(route('employer.applicants.decline', $declined), ['message' => 'We selected another candidate.'])
        ->assertRedirect(route('employer.applicants.show', $declined));
        $this->assertDatabaseHas('job_application_status_histories', [
        'job_application_id' => $declined->id,
        'status' => 'rejected',
        'message' => 'We selected another candidate.',
        ]);

        $withdrawn = $applications->last();
        $candidate = $candidates->last();
        $this->actingAs($candidate, 'web')
        ->post(route('candidate.applications.withdraw', $withdrawn))
        ->assertRedirect(route('candidate.applications.show', $withdrawn));
        $this->assertDatabaseHas('job_application_status_histories', [
        'job_application_id' => $withdrawn->id,
        'status' => 'withdrawn',
        'actor_type' => 'candidate',
        'actor_id' => $candidate->id,
        ]);

        $this->post(route('candidate.applications.withdraw', $withdrawn))->assertStatus(409);
    }

    private function company(string $name = 'Lifecycle Company', string $slug = 'lifecycle-company'): Company
    {
        return Company::create([
            'name' => $name,
            'slug' => $slug,
            'industry' => 'Technology',
            'status' => 'verified',
        ]);
    }

    private function job(Company $company): Job
    {
        return Job::create([
            'company_id' => $company->id,
            'title' => 'Laravel Engineer',
            'slug' => 'laravel-engineer-'.$company->id,
            'category' => 'Engineering',
            'experience_level' => 'Mid level',
            'job_type' => 'Full-time',
            'work_mode' => 'Remote',
            'location' => 'Lagos',
            'summary' => 'Build Laravel applications.',
            'status' => 'open',
        ]);
    }

    private function validPng(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAYAAACtWK6eAAACGElEQVR4nO3ToQHAIADAMI7YdRzGu+MBqN1ERHxNxzPXC5yNrwPgzwwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgMO82f+5nJxrI+isAAAAASUVORK5CYII=', true);
    }
}
