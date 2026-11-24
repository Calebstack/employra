<?php

namespace Tests\Feature;

use App\Support\NigerianStates;
use App\Models\Admin;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployraPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_employra_preview_pages_render(): void
    {
        $paths = [
            '/',
            '/jobs',
            '/companies',
            '/login',
            '/register',
            '/forgot-password',
        ];

        foreach ($paths as $path) {
            $response = $this->get($path);
            $this->assertSame(200, $response->status(), 'Preview path: '.$path);
        }
        foreach (['/candidate', '/candidate/recommended', '/candidate/saved', '/candidate/applications', '/candidate/cvs', '/candidate/profile', '/candidate/settings'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }

        $candidate = User::factory()->create(['role' => 'candidate']);
        CandidateProfile::create(['user_id' => $candidate->id]);
        $this->actingAs($candidate, 'web');
        foreach (['/candidate', '/candidate/recommended', '/candidate/saved', '/candidate/applications', '/candidate/cvs', '/candidate/profile', '/candidate/settings'] as $path) {
            $response = $this->get($path);
            $this->assertSame(200, $response->status(), 'Candidate path: '.$path);
        }
        $this->get('/candidate/cvs')
            ->assertOk()
            ->assertSee('data-cv-upload', false)
            ->assertSee('data-upload-progress', false);

        $company = Company::create([
            'name' => 'BrightPath Technologies',
            'slug' => 'brightpath-tech',
            'industry' => 'Software & IT',
            'status' => 'verified',
            'contact_name' => 'Tunde Bakare',
            'email' => 'tunde@brightpath.example',
            'password' => 'SecurePass123',
            'account_status' => 'active',
        ]);
        Job::create([
            'company_id' => $company->id,
            'title' => 'PHP / Laravel Developer',
            'slug' => 'php-laravel-developer-brightpath',
            'category' => 'Engineering',
            'experience_level' => 'Mid level',
            'job_type' => 'Full-time',
            'work_mode' => 'Hybrid',
            'location' => 'Lagos',
            'salary_min' => 450000,
            'salary_max' => 700000,
            'summary' => 'Build and maintain Laravel applications for banking and education clients as part of a friendly product squad.',
            'responsibilities' => "Build Laravel features\nReview code and mentor junior developers",
            'benefits' => "Health coverage\nFlexible hours",
            'required_skills' => ['PHP', 'Laravel'],
            'preferred_skills' => ['Docker'],
            'status' => 'open',
        ]);

        $this->get('/jobs/php-laravel-developer-brightpath')
            ->assertOk()
            ->assertSee('About the role')
            ->assertSee('Build and maintain Laravel applications for banking and education clients as part of a friendly product squad.')
            ->assertSee('Responsibilities')
            ->assertSee('Review code and mentor junior developers')
            ->assertSee('Benefits')
            ->assertSee('Health coverage');

        $this->actingAs($company, 'company');

        $employerPaths = [
            '/employer',
            '/employer/jobs',
            '/employer/jobs/create',
            '/employer/jobs/1',
            '/employer/interviews',
            '/employer/company',
        ];
        foreach ($employerPaths as $path) {
            $response = $this->get($path);
            $this->assertSame(200, $response->status(), 'Employer preview path: '.$path);
        }
        $this->get('/employer/applicants/11')->assertNotFound();

        $this->actingAs(Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'SecurePass123',
        ]), 'admin');
        $adminPaths = [
            '/admin',
            '/admin/users',
            '/admin/companies',
            '/admin/jobs',
            '/admin/applications',
            '/admin/reports',
            '/admin/ai-log',
            '/admin/settings',
        ];
        foreach ($adminPaths as $path) {
            $response = $this->get($path);
            $this->assertSame(200, $response->status(), 'Admin preview path: '.$path);
        }
    }

    public function test_nigerian_state_catalog_has_36_states_and_the_fct(): void
    {
        $this->assertCount(36, NigerianStates::states());
        $this->assertSame(
            'Federal Capital Territory (FCT)',
            NigerianStates::options()['Federal Capital Territory']
        );
    }

    public function test_jobs_page_filters_and_labels_the_selected_state(): void
    {
        $company = $this->companyWithJobs();
        $this->createJob($company, 'Fleet Coordinator', 'fleet-coordinator', 'Abeokuta', 'On-site');
        $this->createJob($company, 'Remote Data Analyst', 'remote-data-analyst', 'Remote', 'Remote');
        $this->createJob($company, 'Lagos Developer', 'lagos-developer', 'Lagos', 'Hybrid');

        $this->get(route('jobs.index', ['state' => 'Ogun']))
            ->assertOk()
            ->assertSee('Showing results')
            ->assertSee('in Ogun')
            ->assertSee('Fleet Coordinator')
            ->assertSee('Remote Data Analyst')
            ->assertDontSee('Lagos Developer');
    }

    public function test_salary_filter_matches_overlapping_naira_ranges(): void
    {
        $company = $this->companyWithJobs();
        $this->createJob($company, 'PHP / Laravel Developer', 'php-laravel', 'Lagos', 'Hybrid', 450000, 700000);
        $this->createJob($company, 'Junior Data Analyst', 'junior-data', 'Lagos', 'On-site', 200000, 350000);

        $this->get(route('jobs.index', ['salary_min' => 450000, 'salary_max' => 500000]))
            ->assertOk()
            ->assertSee('1 job found')
            ->assertSee('PHP / Laravel Developer')
            ->assertDontSee('Junior Data Analyst')
            ->assertSee('Monthly salary (NGN)')
            ->assertSee('type="range"', false)
            ->assertSee('id="f-salary-min"', false)
            ->assertSee('from ₦450,000')
            ->assertSee('up to ₦500,000');
    }

    public function test_salary_filter_rejects_an_inverted_range_without_redirect_loop(): void
    {
        $this->followingRedirects()
            ->get(route('jobs.index', ['salary_min' => 600000, 'salary_max' => 200000]))
            ->assertOk()
            ->assertSee('The maximum salary must be at least the minimum salary.')
            ->assertSee('600000')
            ->assertSee('200000');
    }

    public function test_salary_filter_accepts_a_single_bound(): void
    {
        $company = $this->companyWithJobs();
        $this->createJob($company, 'Frontend Developer Intern', 'frontend-intern', 'Lagos', 'Hybrid', 100000, 140000);
        $this->createJob($company, 'Senior Designer', 'senior-designer', 'Lagos', 'On-site', 500000, 700000);

        $this->get(route('jobs.index', ['salary_max' => 150000]))
            ->assertOk()
            ->assertSee('1 job found')
            ->assertSee('Frontend Developer Intern')
            ->assertDontSee('PHP / Laravel Developer');
    }

    public function test_demo_job_slug_is_not_displayed_as_a_database_job(): void
    {
        $this->get(route('jobs.show', 'php-laravel-developer-brightpath'))->assertNotFound();
        $this->get(route('jobs.index'))->assertOk()->assertDontSee('PHP / Laravel Developer');
    }

    private function companyWithJobs(): Company
    {
        return Company::create([
            'name' => 'Jobs Test Company',
            'slug' => 'jobs-test-company',
            'industry' => 'Technology',
            'status' => 'verified',
        ]);
    }

    private function createJob(Company $company, string $title, string $slug, string $location, string $mode, ?int $minimum = null, ?int $maximum = null): Job
    {
        return Job::create([
            'company_id' => $company->id,
            'title' => $title,
            'slug' => $slug,
            'category' => 'Engineering',
            'experience_level' => 'Mid level',
            'job_type' => 'Full-time',
            'work_mode' => $mode,
            'location' => $location,
            'salary_min' => $minimum,
            'salary_max' => $maximum,
            'summary' => 'A persisted job listing.',
            'status' => 'open',
        ]);
    }
}
