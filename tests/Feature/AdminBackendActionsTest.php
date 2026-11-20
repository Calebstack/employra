<?php

namespace Tests\Feature;

use App\Models\AdminActivityLog;
use App\Models\Admin;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Cv;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\PlatformSetting;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBackendActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_jobs_are_persisted_listed_and_moderated_by_admin(): void
    {
        $company = $this->createCompanyFor();
        $admin = $this->createAdmin();

        $this->actingAs($company, 'company')->post(route('employer.jobs.store'), $this->jobPayload())
            ->assertRedirect(route('employer.jobs'))
            ->assertSessionHas('status', 'Your job has been published.');

        $job = Job::where('title', 'Backend Engineer')->firstOrFail();
        $this->assertSame($company->id, $job->company_id);
        $this->assertSame(['PHP', 'Laravel'], $job->required_skills);
        $this->assertDatabaseHas('employer_jobs', ['id' => $job->id, 'status' => 'open']);
        $this->get(route('jobs.show', $job->slug))
            ->assertOk()
            ->assertSee('Backend Engineer')
            ->assertSee($company->name);

        $this->actingAs($admin, 'admin')->get(route('admin.jobs', ['status' => 'Open', 'q' => 'Backend']))
            ->assertOk()
            ->assertSee('Backend Engineer')
            ->assertSee($company->name);
        $this->get(route('admin.jobs.show', $job))
            ->assertOk()
            ->assertSee('Backend Engineer')
            ->assertSee('Build APIs')
            ->assertSee('Laravel')
            ->assertSee('Health coverage');

        $this->post(route('admin.jobs.take-down', $job), ['reason' => 'Policy violation'])
            ->assertRedirect(route('admin.jobs'));
        $this->assertDatabaseHas('employer_jobs', ['id' => $job->id, 'status' => 'taken_down', 'moderation_note' => 'Policy violation']);

        $this->post(route('admin.jobs.keep-live', $job))->assertRedirect(route('admin.jobs'));
        $this->assertDatabaseHas('employer_jobs', ['id' => $job->id, 'status' => 'open']);

        $this->actingAs($company, 'company')->post(route('employer.jobs.close', $job))
            ->assertRedirect(route('employer.jobs'));
        $this->assertDatabaseHas('employer_jobs', ['id' => $job->id, 'status' => 'closed']);
    }

    public function test_candidate_job_report_action_moderates_job_and_records_audit(): void
    {
        $company = $this->createCompanyFor();
        $job = $this->createJob($company);
        $candidate = User::factory()->create(['role' => 'candidate']);
        $admin = $this->createAdmin();

        $this->actingAs($candidate)->post(route('jobs.report', $job->slug), [
            'reason' => 'Asks candidates for payment',
            'details' => 'The listing asks applicants to pay before an interview.',
        ])->assertRedirect();

        $report = Report::where('target_type', 'job')->firstOrFail();
        $this->assertSame('open', $report->status);

        $this->actingAs($admin, 'admin')->get(route('admin.reports', ['status' => 'open']))
            ->assertOk()
            ->assertSee('Backend Engineer')
            ->assertSee('Take action');

        $this->post(route('admin.reports.action', $report), ['resolution_note' => 'Listing removed after review.'])
            ->assertRedirect(route('admin.reports'));

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'actioned']);
        $this->assertDatabaseHas('employer_jobs', ['id' => $job->id, 'status' => 'taken_down']);
        $this->assertSame(1, AdminActivityLog::where('action', 'report.actioned')->count());
        $this->get(route('jobs.show', $job->slug))->assertNotFound();
    }

    public function test_candidates_can_report_a_company_and_admin_sees_the_report(): void
    {
        $company = $this->createCompanyFor();
        $candidate = User::factory()->create(['name' => 'Reporting Candidate', 'role' => 'candidate']);
        $admin = $this->createAdmin();

        $this->actingAs($candidate, 'web')
            ->post(route('companies.report', $company->slug), [
                'reason' => 'Fraudulent company information',
                'details' => 'The company profile contains information that appears to be fraudulent.',
            ])
            ->assertRedirect(route('companies.show', $company->slug))
            ->assertSessionHas('status', 'Your company report was submitted for review.');

        $report = Report::where('target_type', 'company')->firstOrFail();
        $this->assertSame($company->id, $report->target_id);
        $this->assertSame('open', $report->status);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.companies.show', $company))
            ->assertOk()
            ->assertSee('Fraudulent company information')
            ->assertSee('The company profile contains information that appears to be fraudulent.')
            ->assertSee('Reporting Candidate');

        $this->post(route('admin.reports.action', $report), [
            'resolution_note' => 'Company profile rejected after review.',
        ])->assertRedirect(route('admin.reports'));

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'actioned']);
    }

    public function test_candidate_applications_are_saved_and_visible_to_the_company_and_admin(): void
    {
        $company = $this->createCompanyFor();
        $job = $this->createJob($company);
        $admin = $this->createAdmin();
        $candidate = User::factory()->create([
            'name' => 'Candidate Applicant',
            'email' => 'candidate-applicant@example.com',
            'role' => 'candidate',
        ]);
        CandidateProfile::create(['user_id' => $candidate->id]);

        $this->actingAs($candidate)->get(route('jobs.apply', $job->slug))
            ->assertOk()
            ->assertSee('Apply for Backend Engineer');
        $this->post(route('jobs.apply.submit', $job->slug), [
            'cover_note' => 'I have relevant Laravel experience.',
            'consent' => '1',
        ])->assertRedirect(route('candidate.applications'))
            ->assertSessionHas('status', 'Your application has been submitted.');

        $application = JobApplication::where('user_id', $candidate->id)->where('job_id', $job->id)->firstOrFail();
        $this->assertSame('submitted', $application->status);

        $this->get(route('candidate.applications'))
            ->assertOk()
            ->assertSee('Backend Engineer')
            ->assertSee('Submitted');
        $this->post(route('jobs.apply.submit', $job->slug), [
            'consent' => '1',
        ])->assertRedirect(route('candidate.applications'))
            ->assertSessionHas('status', 'You have already applied for this job.');
        $this->assertSame(1, JobApplication::count());

        $this->actingAs($company, 'company')->get(route('employer.dashboard'))
            ->assertOk()
            ->assertSee('Candidate Applicant')
            ->assertSee('Backend Engineer');
        $this->get(route('employer.jobs.show', $job))
            ->assertOk()
            ->assertSee('candidate-applicant@example.com')
            ->assertSee('I have relevant Laravel experience.');

        $this->actingAs($admin, 'admin')->get(route('admin.applications'))
            ->assertOk()
            ->assertSee('Candidate Applicant')
            ->assertSee('Backend Company')
            ->assertSee('Backend Engineer');
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Applications');
    }

    public function test_admin_dashboard_and_flagged_jobs_use_persisted_report_counts_and_job_states(): void
    {
        $company = $this->createCompanyFor();
        $admin = $this->createAdmin();
        User::factory()->create(['role' => 'admin']);
        $openJob = $this->createJob($company);
        $closedJob = Job::create(array_merge($this->jobPayload(), [
            'company_id' => $company->id,
            'title' => 'Closed Backend Engineer',
            'slug' => 'closed-backend-engineer',
            'status' => 'closed',
        ]));
        $takenDownJob = Job::create(array_merge($this->jobPayload(), [
            'company_id' => $company->id,
            'title' => 'Taken Down Backend Engineer',
            'slug' => 'taken-down-backend-engineer',
            'status' => 'taken_down',
        ]));
        $candidate = User::factory()->create(['role' => 'candidate']);
        JobApplication::create([
            'user_id' => $candidate->id,
            'job_id' => $closedJob->id,
            'status' => 'submitted',
        ]);

        foreach ([$openJob, $closedJob] as $job) {
            Report::create([
                'target_type' => 'job',
                'target_id' => $job->id,
                'target_label' => $job->title,
                'reason' => 'Potentially misleading',
                'details' => 'Needs review.',
                'status' => 'open',
            ]);
        }

        $dashboard = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Flagged job posts')
            ->assertSee('Applications')
            ->assertSee('class="num">1</div><div class="lbl">Users</div>', false)
            ->assertSee('0');
        $dashboard->assertSee(route('admin.reports', ['status' => 'open']), false);
        $this->get(route('admin.reports', ['status' => 'open']))
            ->assertOk()
            ->assertSee('Potentially misleading');
        $this->get(route('admin.applications'))
            ->assertOk()
            ->assertSee($candidate->name)
            ->assertSee(route('admin.users.show', $candidate), false);

        $this->get(route('admin.jobs', ['status' => 'Flagged', 'q' => 'Closed Backend Engineer']))
            ->assertOk()
            ->assertSee('Closed Backend Engineer')
            ->assertSee('Closed')
            ->assertDontSee('Take down')
            ->assertDontSee('Keep live')
            ->assertSee('>1<', false)
            ->assertDontSee('Not tracked');

        $this->get(route('admin.jobs', ['status' => 'Flagged', 'q' => 'Backend Engineer']))
            ->assertOk()
            ->assertSee('Flagged')
            ->assertSee('Take down');
        $this->get(route('admin.jobs', ['status' => 'Closed']))
            ->assertOk()
            ->assertSee('Closed Backend Engineer')
            ->assertDontSee('Taken Down Backend Engineer');
        $this->get(route('admin.jobs', ['status' => 'Taken down']))
            ->assertOk()
            ->assertSee('Taken Down Backend Engineer');
    }

    public function test_admin_can_contact_reporters_dismiss_reports_and_filter_export_activity(): void
    {
        Mail::fake();
        $admin = $this->createAdmin();
        $contactReport = Report::create([
            'target_type' => 'job',
            'target_label' => 'Reported job',
            'reason' => 'Scam',
            'details' => 'The listing asks for payment.',
            'reporter_email' => 'reporter@example.com',
            'status' => 'open',
        ]);
        $dismissReport = Report::create([
            'target_type' => 'company',
            'target_label' => 'Company report',
            'reason' => 'Incorrect information',
            'details' => 'This concern could not be substantiated.',
            'status' => 'open',
        ]);

        $this->actingAs($admin, 'admin')->post(route('admin.reports.contact', $contactReport), [
            'message' => 'We have received and reviewed your report.',
        ])->assertRedirect(route('admin.reports'));
        $this->assertDatabaseHas('admin_activity_logs', ['action' => 'report.contacted']);

        $this->post(route('admin.reports.dismiss', $dismissReport))
            ->assertRedirect(route('admin.reports'));
        $this->assertDatabaseHas('reports', ['id' => $dismissReport->id, 'status' => 'dismissed']);

        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => 'settings.updated',
            'target_type' => 'settings',
            'details' => 'Changed platform settings.',
        ]);
        $this->get(route('admin.audit', ['type' => 'settings', 'range' => '365']))
            ->assertOk()
            ->assertSee('Settings Updated')
            ->assertDontSee('Report contacted');
        $export = $this->get(route('admin.audit.export', ['type' => 'settings', 'range' => '365']))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('settings.updated', $export->streamedContent());
        $this->assertStringNotContainsString('report.contacted', $export->streamedContent());
    }

    public function test_report_action_rejecting_company_hides_its_existing_jobs_from_public_pages(): void
    {
        $company = $this->createCompanyFor();
        $job = $this->createJob($company);
        $admin = $this->createAdmin();
        $report = Report::create([
            'target_type' => 'company',
            'target_id' => $company->id,
            'target_label' => $company->name,
            'reason' => 'Misleading information',
            'details' => 'Company details could not be verified.',
            'status' => 'open',
        ]);

        $this->actingAs($admin, 'admin')->post(route('admin.reports.action', $report))
            ->assertRedirect(route('admin.reports'));

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('employer_jobs', ['id' => $job->id, 'status' => 'open']);
        $this->get(route('jobs.index'))->assertOk()->assertDontSee($job->title);
        $this->get(route('jobs.show', $job->slug))->assertNotFound();
        $this->get(route('companies.show', $company->slug))->assertNotFound();
    }

    public function test_admin_can_create_another_admin_account(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'admin')->post(route('admin.admins.store'), [
            'name' => 'New Administrator',
            'email' => 'new-admin@example.com',
            'password' => 'strong-password-123',
            'password_confirmation' => 'strong-password-123',
        ])->assertRedirect(route('admin.settings'));

        $createdAdmin = Admin::where('email', 'new-admin@example.com')->firstOrFail();
        $this->assertSame('admin', $createdAdmin->role);
        $this->assertSame('active', $createdAdmin->status);
        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $admin->id,
            'action' => 'admin.created',
            'target_id' => $createdAdmin->id,
        ]);
    }

    public function test_admin_user_filters_suspend_reactivate_reset_and_export_use_real_users(): void
    {
        Mail::fake();
        Password::shouldReceive('sendResetLink')->once()->andReturn(Password::RESET_LINK_SENT);

        $admin = $this->createAdmin();
        $candidate = User::factory()->create([
            'name' => 'Adaeze Okafor',
            'email' => 'adaeze@example.com',
            'role' => 'candidate',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'admin')->get(route('admin.users', [
            'role' => 'candidate',
            'status' => 'active',
            'q' => 'Adaeze',
        ]))->assertOk()->assertSee('Adaeze Okafor');

        $this->post(route('admin.users.suspend', $candidate))->assertRedirect(route('admin.users'));
        $this->assertDatabaseHas('users', ['id' => $candidate->id, 'status' => 'suspended']);
        $this->get(route('admin.users', ['status' => 'suspended']))->assertOk()->assertSee('Adaeze Okafor');
        $this->post(route('admin.users.reactivate', $candidate))->assertRedirect(route('admin.users'));
        $this->post(route('admin.users.reset-password', $candidate))->assertRedirect(route('admin.users'));
        $this->post(route('admin.users.message', $candidate), ['subject' => 'Account update', 'message' => 'Please review your account details.'])
            ->assertRedirect(route('admin.users'));

        $export = $this->get(route('admin.users.export', ['role' => 'candidate']))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('adaeze@example.com', $export->streamedContent());
    }

    public function test_admin_can_review_candidate_profile_applications_and_download_cv_with_audit(): void
    {
        Storage::fake('local');
        $company = $this->createCompanyFor();
        $job = $this->createJob($company);
        $admin = $this->createAdmin();
        $candidate = User::factory()->create([
            'name' => 'Candidate With Profile',
            'email' => 'candidate-profile@example.com',
            'role' => 'candidate',
        ]);
        $profile = CandidateProfile::create([
            'user_id' => $candidate->id,
            'headline' => 'Senior Laravel Developer',
            'phone' => '08012345678',
            'location' => 'Lagos',
            'summary' => 'Experienced engineer focused on reliable web applications.',
            'portfolio_url' => 'https://portfolio.example',
            'preferred_roles' => ['Backend Engineer'],
            'preferred_work_modes' => ['remote'],
            'preferred_locations' => ['Lagos'],
            'expected_salary_min' => 250000,
            'expected_salary_max' => 500000,
            'availability' => 'immediately',
            'visibility' => 'employers_i_apply_to',
            'completion' => 90,
        ]);

        $this->actingAs($candidate)->post(route('candidate.cvs.upload'), [
            'cv' => UploadedFile::fake()->createWithContent('candidate-resume.pdf', "%PDF-1.4\nResume test"),
        ])->assertRedirect(route('candidate.cvs'));

        $cv = $profile->cvs()->firstOrFail();
        JobApplication::create([
            'user_id' => $candidate->id,
            'job_id' => $job->id,
            'cv_id' => $cv->id,
            'status' => 'submitted',
            'cover_note' => 'Please consider my application and attached resume.',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.show', $candidate))
            ->assertOk()
            ->assertSee('Senior Laravel Developer')
            ->assertSee('08012345678')
            ->assertSee('Experienced engineer focused on reliable web applications.')
            ->assertSee('candidate-resume.pdf')
            ->assertSee('Please consider my application and attached resume.')
            ->assertSee('Backend Engineer');

        $this->get(route('admin.users.cvs.download', [$candidate, $cv]))
            ->assertOk()
            ->assertDownload('candidate-resume.pdf');
        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $admin->id,
            'action' => 'user.cv_downloaded',
            'target_type' => 'user',
            'target_id' => $candidate->id,
        ]);

        $anotherCandidate = User::factory()->create(['role' => 'candidate']);
        CandidateProfile::create(['user_id' => $anotherCandidate->id]);
        $this->get(route('admin.users.cvs.download', [$anotherCandidate, $cv]))->assertNotFound();

        Auth::guard('admin')->logout();
        $this->actingAs($candidate, 'web')
            ->get(route('admin.users.cvs.download', [$candidate, $cv]))
            ->assertRedirect();
    }

    public function test_settings_are_saved_and_registration_toggle_takes_effect(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'admin')->post(route('admin.settings.update'), [
            'platform_name' => 'Employra NG',
            'support_email' => 'help@example.com',
            'job_categories' => "Engineering\nHealthcare",
        ])->assertRedirect(route('admin.settings'));

        $this->assertSame(['Engineering', 'Healthcare'], PlatformSetting::find('job_categories')->value);
        $this->assertFalse(PlatformSetting::find('allow_registration')->value);
        $this->post(route('logout'));
        $this->get(route('register'))->assertForbidden();
    }

    public function test_company_can_save_profile_details_and_logo_before_verification(): void
    {
        Storage::fake('public');
        $company = $this->createCompanyFor();
        $company->forceFill(['status' => 'pending'])->save();

        $this->actingAs($company, 'company')
            ->get(route('employer.company'))
            ->assertOk()
            ->assertSee('Company profile');

        $this->post(route('employer.company.update'), [
            'name' => 'Expanded Backend Company',
            'contact_name' => 'Company Contact',
            'industry' => 'Software & IT',
            'website' => 'https://backend.example',
            'location' => 'Lagos, Nigeria',
            'company_size' => '11–50 employees',
            'description' => 'We build software products for growing companies.',
            'registration_number' => 'RC-12345',
            'logo' => UploadedFile::fake()->createWithContent(
                'company-logo.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAIAAAAiOjnJAAACFUlEQVR4nO3SQQkAIADAQDvZyU62tYRDkIMLsMfGXBuuG88L+JKxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi4SxSBiLhLFIGIuEsUgYi104vCDPZ6HNWb8AAAAASUVORK5CYII=')
            ),
        ])->assertRedirect(route('employer.company'));

        $company->refresh();
        $this->assertSame('Expanded Backend Company', $company->name);
        $this->assertSame('RC-12345', $company->registration_number);
        $this->assertNotNull($company->logo_path);
        Storage::disk('public')->assertExists($company->logo_path);

        $company->forceFill(['status' => 'verified'])->save();
        $this->get(route('companies.show', $company->slug))
            ->assertOk()
            ->assertSee('We build software products for growing companies.')
            ->assertSee('Lagos, Nigeria')
            ->assertSee('backend.example');
    }

    public function test_admin_company_details_show_related_data_and_delete_company_records(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $company = $this->createCompanyFor();
        $logoPath = 'company-logos/backend-company.png';
        Storage::disk('public')->put($logoPath, 'logo');
        $company->forceFill([
            'location' => 'Lagos',
            'company_size' => '11–50 employees',
            'description' => 'Backend company description.',
            'website' => 'https://backend.example',
            'registration_number' => 'RC-67890',
            'logo_path' => $logoPath,
        ])->save();
        $job = $this->createJob($company);
        $candidate = User::factory()->create(['name' => 'Candidate Applicant']);
        $profile = CandidateProfile::create(['user_id' => $candidate->id]);
        $cv = Cv::create([
            'candidate_profile_id' => $profile->id,
            'original_name' => 'candidate.pdf',
            'stored_path' => 'cvs/'.$profile->id.'/candidate.pdf',
            'mime' => 'application/pdf',
            'size_bytes' => 100,
            'sha256' => str_repeat('a', 64),
            'status' => 'uploaded',
            'is_primary' => true,
        ]);
        Storage::disk('local')->put($cv->stored_path, 'candidate cv');
        $application = JobApplication::create([
            'user_id' => $candidate->id,
            'job_id' => $job->id,
            'cv_id' => $cv->id,
            'status' => 'submitted',
            'cover_note' => 'Please consider my application.',
        ]);
        $companyReport = Report::create([
            'target_type' => 'company',
            'target_id' => $company->id,
            'target_label' => $company->name,
            'reason' => 'Misleading company information',
            'details' => 'Company details need review.',
            'reported_by' => $candidate->id,
            'reporter_name' => $candidate->name,
            'reporter_email' => $candidate->email,
            'severity' => 'high',
            'status' => 'open',
        ]);
        $jobReport = Report::create([
            'target_type' => 'job',
            'target_id' => $job->id,
            'target_label' => $job->title,
            'reason' => 'Misleading information',
            'details' => 'The listing needs review.',
            'reported_by' => $candidate->id,
            'reporter_name' => $candidate->name,
            'reporter_email' => $candidate->email,
            'severity' => 'medium',
            'status' => 'open',
        ]);
        $admin = $this->createAdmin();
        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => 'company.verified',
            'target_type' => 'company',
            'target_id' => $company->id,
            'details' => $company->name,
        ]);
        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => 'job.taken_down',
            'target_type' => 'job',
            'target_id' => $job->id,
            'details' => $job->title,
        ]);
        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => 'report.actioned',
            'target_type' => 'report',
            'target_id' => $jobReport->id,
            'details' => $jobReport->target_label,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.companies.show', $company))
            ->assertOk()
            ->assertSee('RC-67890')
            ->assertSee('Backend company description.')
            ->assertSee('Backend Engineer')
            ->assertSee('Company details need review.')
            ->assertSee('The listing needs review.')
            ->assertSee('1 applications');

        $this->delete(route('admin.companies.destroy', $company))
            ->assertRedirect(route('admin.companies'))
            ->assertSessionHas('status', 'Company profile and related data were permanently deleted.');

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
        $this->assertDatabaseMissing('employer_jobs', ['id' => $job->id]);
        $this->assertDatabaseMissing('job_applications', ['id' => $application->id]);
        $this->assertDatabaseMissing('reports', ['id' => $companyReport->id]);
        $this->assertDatabaseMissing('reports', ['id' => $jobReport->id]);
        $this->assertDatabaseHas('users', ['id' => $candidate->id]);
        $this->assertDatabaseHas('cvs', ['id' => $cv->id]);
        Storage::disk('local')->assertExists($cv->stored_path);
        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'company.permanently_deleted',
            'target_type' => null,
            'target_id' => null,
        ]);
        $this->assertDatabaseMissing('admin_activity_logs', [
            'action' => 'company.verified',
            'target_type' => 'company',
            'target_id' => $company->id,
        ]);
        $this->assertDatabaseMissing('admin_activity_logs', [
            'action' => 'job.taken_down',
            'target_type' => 'job',
            'target_id' => $job->id,
        ]);
        $this->assertDatabaseMissing('admin_activity_logs', [
            'action' => 'report.actioned',
            'target_type' => 'report',
            'target_id' => $jobReport->id,
        ]);
        Storage::disk('public')->assertMissing($logoPath);
    }

    private function createCompanyFor(): Company
    {
        return Company::create([
            'name' => 'Backend Company',
            'slug' => 'backend-company-'.fake()->unique()->numerify('###'),
            'industry' => 'Technology',
            'status' => 'verified',
            'contact_name' => 'Company Contact',
            'email' => 'company-'.fake()->unique()->numerify('###').'@example.com',
            'password' => 'SecurePass123',
            'account_status' => 'active',
        ]);
    }

    private function createJob(Company $company): Job
    {
        return Job::create(array_merge($this->jobPayload(), [
            'company_id' => $company->id,
            'slug' => 'backend-engineer-'.$company->id,
            'status' => 'open',
        ]));
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'name' => 'Employra Admin',
            'email' => 'admin@example.com',
            'password' => 'SecurePass123',
        ]);
    }

    private function jobPayload(): array
    {
        return [
            'title' => 'Backend Engineer',
            'category' => 'Engineering',
            'experience_level' => 'Mid level',
            'job_type' => 'Full-time',
            'work_mode' => 'Hybrid',
            'location' => 'Lagos',
            'salary_min' => 450000,
            'salary_max' => 700000,
            'summary' => 'Build and maintain backend services.',
            'responsibilities' => "Build APIs\nReview code",
            'benefits' => "Health coverage\nFlexible hours",
            'required_skills' => ['PHP', 'Laravel'],
            'preferred_skills' => ['Docker'],
            'min_experience_years' => 2,
            'minimum_education' => 'HND / B.Sc.',
            'notify_applicants' => true,
            'status' => 'open',
        ];
    }
}
