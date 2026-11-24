<?php

namespace Tests\Feature;

use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CandidateOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_preferences_and_optional_cv_are_saved_privately(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'candidate']);
        $profile = CandidateProfile::create(['user_id' => $user->id]);
        $company = Company::create([
            'name' => 'Application Company',
            'slug' => 'application-company',
            'industry' => 'Technology',
            'status' => 'verified',
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
            'summary' => 'Build Laravel applications.',
            'status' => 'open',
        ]);
        $cv = UploadedFile::fake()->createWithContent(
            'resume.pdf',
            "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"
        );

        $this->actingAs($user)
            ->post(route('candidate.onboarding.store'), $this->validOnboardingData([
                'phone' => '',
                'cv' => $cv,
            ]))
            ->assertRedirect(route('jobs.index'));

        $profile->refresh();
        $this->assertSame(['Product Manager', 'UX Designer'], $profile->preferred_roles);
        $this->assertSame(['remote', 'hybrid'], $profile->preferred_work_modes);
        $this->assertSame(['Lagos', 'Oyo'], $profile->preferred_locations);
        $this->assertNull($profile->phone);
        $this->assertSame(250000, $profile->expected_salary_min);
        $this->assertSame(750000, $profile->expected_salary_max);
        $this->assertNotNull($profile->onboarding_completed_at);

        $document = $profile->cvs()->firstOrFail();
        $this->assertSame('resume.pdf', $document->original_name);
        $this->assertSame('uploaded', $document->status);
        $this->assertTrue($document->is_primary);
        $this->assertStringStartsWith('cvs/'.$profile->id.'/', $document->stored_path);
        Storage::disk('local')->assertExists($document->stored_path);

        $this->get(route('candidate.cvs.download', $document->id))
            ->assertOk()
            ->assertDownload('resume.pdf');

        $this->get(route('candidate.cvs'))
            ->assertOk()
            ->assertSee('resume.pdf')
            ->assertSee('Uploaded');
        $this->get(route('jobs.apply', 'php-laravel-developer-brightpath'))
            ->assertOk()
            ->assertSee('resume.pdf');
    }

    public function test_candidate_can_finish_onboarding_without_phone_or_cv(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);
        $profile = CandidateProfile::create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('candidate.onboarding.store'), $this->validOnboardingData())
            ->assertRedirect(route('jobs.index'));

        $profile->refresh();
        $this->assertNull($profile->phone);
        $this->assertNotNull($profile->onboarding_completed_at);
        $this->assertCount(0, $profile->cvs);
    }

    public function test_candidate_can_register_complete_onboarding_and_remain_signed_in(): void
    {
        $this->post(route('register.submit'), [
            'name' => 'A Candidate',
            'email' => 'candidate-onboarding@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
            'role' => 'candidate',
            'terms' => '1',
        ])->assertRedirect(route('candidate.onboarding'));

        $user = User::where('email', 'candidate-onboarding@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->get(route('candidate.onboarding'))->assertOk();

        $this->post(route('candidate.onboarding.store'), $this->validOnboardingData())
            ->assertRedirect(route('jobs.index'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('jobs.index'))->assertOk();
    }

    public function test_candidate_can_update_profile_and_store_a_private_profile_photo(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'candidate', 'name' => 'Old Candidate Name']);
        CandidateProfile::create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('candidate.profile.update'), [
                'first_name' => 'Adaeze',
                'last_name' => 'Okafor',
                'headline' => 'Laravel Developer',
                'availability' => 'within_2_weeks',
                'preferred_locations' => [''],
                'photo' => UploadedFile::fake()->createWithContent(
                    'profile.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAYAAACtWK6eAAACGElEQVR4nO3ToQHAIADAMI7YdRzGu+MBqN1ERHxNxzPXC5yNrwPgzwwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgEAwCwSAQDALBIBAMAsEgMO82f+5nJxrI+isAAAAASUVORK5CYII=')
                ),
            ])
            ->assertRedirect(route('candidate.profile'))
            ->assertSessionHas('status', 'Your profile has been updated.');

        $user->refresh();
        $profile = $user->candidateProfile;
        $this->assertSame('Adaeze Okafor', $user->name);
        $this->assertSame('Laravel Developer', $profile->headline);
        $this->assertSame('within_2_weeks', $profile->availability);
        $this->assertSame([], $profile->preferred_locations);
        $this->assertNotNull($profile->photo_path);
        Storage::disk('local')->assertExists($profile->photo_path);

        $this->get(route('candidate.profile'))
            ->assertOk()
            ->assertSee('Laravel Developer')
            ->assertSee(route('candidate.profile.photo'))
            ->assertSee('candidateProfilePhotoPreview')
            ->assertSee('View larger profile photo', false);
        $this->get(route('candidate.profile.photo'))->assertOk();
    }

    public function test_salary_range_must_be_in_ascending_order(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($user)
            ->from(route('candidate.onboarding'))
            ->post(route('candidate.onboarding.store'), $this->validOnboardingData([
                'expected_salary_min' => 800000,
                'expected_salary_max' => 500000,
            ]))
            ->assertRedirect(route('candidate.onboarding'))
            ->assertSessionHasErrors('expected_salary_max');
    }

    public function test_onboarding_rejects_locations_that_are_not_nigerian_states_or_fct(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($user)
            ->from(route('candidate.onboarding'))
            ->post(route('candidate.onboarding.store'), $this->validOnboardingData([
                'preferred_locations' => ['Abuja'],
            ]))
            ->assertRedirect(route('candidate.onboarding'))
            ->assertSessionHasErrors('preferred_locations.0');
    }

    public function test_uploaded_cv_cannot_be_downloaded_by_another_candidate(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($owner)
            ->post(route('candidate.cvs.upload'), [
                'cv' => UploadedFile::fake()->createWithContent(
                    'private.pdf',
                    "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"
                ),
            ])
            ->assertRedirect(route('candidate.cvs'));

        $document = $owner->candidateProfile->cvs()->firstOrFail();
        $otherCandidate = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($otherCandidate)
            ->get(route('candidate.cvs.download', $document->id))
            ->assertNotFound();
    }

    public function test_incomplete_candidate_can_return_home_without_an_onboarding_redirect_loop(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertRedirect(route('jobs.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_onboarding_validation_errors_are_summarized_for_the_candidate(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($user)
            ->from(route('candidate.onboarding'))
            ->post(route('candidate.onboarding.store'), $this->validOnboardingData([
                'preferred_work_modes' => [],
            ]))
            ->assertRedirect(route('candidate.onboarding'))
            ->assertSessionHasErrors('preferred_work_modes');

        $this->get(route('candidate.onboarding'))
            ->assertOk()
            ->assertSee("We couldn't save your profile yet. Please correct these fields:", false);
    }

    private function validOnboardingData(array $overrides = []): array
    {
        return array_merge([
            'preferred_roles' => ['Product Manager', 'UX Designer'],
            'preferred_work_modes' => ['remote', 'hybrid'],
            'preferred_locations' => ['Lagos', 'Oyo'],
            'availability' => 'immediately',
            'phone' => null,
            'expected_salary_min' => 250000,
            'expected_salary_max' => 750000,
        ], $overrides);
    }
}
