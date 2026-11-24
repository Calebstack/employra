<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_registration_creates_a_profile_and_signs_in(): void
    {
        $response = $this->post(route('register.submit'), [
            'name' => 'A Candidate',
            'email' => 'CANDIDATE@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
            'role' => 'candidate',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('candidate.onboarding'));
        $response->assertSessionHas('status', 'Your account has been created.');
        $user = User::where('email', 'candidate@example.com')->firstOrFail();

        $this->assertDatabaseHas('candidate_profiles', ['user_id' => $user->id]);
        $this->assertTrue(Hash::check('SecurePass123', $user->password));
        $this->assertAuthenticatedAs($user);

        $this->get(route('candidate.onboarding'))
            ->assertOk()
            ->assertSee('Your account has been created.')
            ->assertSee('What are you looking for?')
            ->assertSee('Select a state')
            ->assertSee('Federal Capital Territory (FCT)')
            ->assertSee('Desired monthly salary');
    }

    public function test_authenticated_candidate_sees_limited_navigation_and_profile_links(): void
    {
        $user = User::factory()->create([
            'name' => 'Olusegun Kele',
            'role' => 'candidate',
        ]);
        CandidateProfile::create([
            'user_id' => $user->id,
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->followingRedirects()
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Find your next job')
            ->assertDontSee('AI-assisted hiring, people in charge')
            ->assertDontSee('Ready to find, hire and grow?')
            ->assertSee('Find Jobs')
            ->assertSee('Companies')
            ->assertSee('My profile')
            ->assertSee('Resume')
            ->assertSee('Account settings')
            ->assertSee('Notification settings')
            ->assertSee('Sign out')
            ->assertDontSee('Career Tools')
            ->assertDontSee('For Employers')
            ->assertDontSee('Create Account');
    }

    public function test_company_registration_creates_a_company_account_and_signs_in(): void
    {
        $response = $this->post(route('register.submit'), [
            'name' => 'Company Contact',
            'email' => 'company@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
            'role' => 'company',
            'company_name' => 'Example Company',
            'industry' => 'Technology',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('employer.dashboard'));
        $company = Company::where('email', 'company@example.com')->firstOrFail();

        $this->assertSame('pending', $company->status);
        $this->assertSame('Company Contact', $company->contact_name);
        $this->assertTrue(Hash::check('SecurePass123', $company->password));
        $this->assertDatabaseMissing('users', ['email' => 'company@example.com']);
        $this->assertAuthenticatedAs($company, 'company');
        $this->get(route('employer.dashboard'))
            ->assertOk()
            ->assertSee('Awaiting verification')
            ->assertSee('Example Company');
        $this->get(route('employer.jobs'))->assertRedirect(route('employer.dashboard'));
    }

    public function test_registration_does_not_allow_admin_role(): void
    {
        $response = $this->from(route('register'))->post(route('register.submit'), [
            'name' => 'An Admin',
            'email' => 'admin@example.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
            'role' => 'admin',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('register'))->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
        $this->assertDatabaseMissing('admins', ['email' => 'admin@example.com']);
    }

    public function test_login_authenticates_an_active_user_and_logout_invalidates_the_session(): void
    {
        $user = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => 'SecurePass123',
            'status' => 'active',
        ]);
        CandidateProfile::create([
            'user_id' => $user->id,
            'onboarding_completed_at' => now(),
        ]);

        $this->post(route('login.submit'), [
            'email' => 'CANDIDATE@example.com',
            'password' => 'SecurePass123',
            'redirect' => route('companies.index'),
        ])->assertRedirect(route('jobs.index'))->assertSessionHas('status', 'You are now signed in.');

        $this->assertAuthenticatedAs($user);
        $this->get(route('jobs.index'))
            ->assertOk()
            ->assertSee('Welcome back, '.$user->name.'.')
            ->assertSee('Your job search and application updates are ready.')
            ->assertDontSee('You are now signed in.');

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_admin_logout_redirects_to_the_public_homepage(): void
    {
        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'SecurePass123',
            'status' => 'active',
        ]);

        $this->post(route('login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'SecurePass123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Welcome back, Admin User.')
            ->assertSee('Your administration workspace is ready.')
            ->assertDontSee('You are now signed in.');

        $this->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest('admin');
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Find the right job.');
    }

    public function test_login_sends_incomplete_candidates_to_onboarding(): void
    {
        $user = User::factory()->create([
            'email' => 'incomplete@example.com',
            'password' => 'SecurePass123',
            'role' => 'candidate',
            'status' => 'active',
        ]);
        CandidateProfile::create(['user_id' => $user->id]);

        $this->post(route('login.submit'), [
            'email' => 'incomplete@example.com',
            'password' => 'SecurePass123',
        ])->assertRedirect(route('candidate.onboarding'));
    }

    public function test_company_login_and_home_visits_open_the_verification_page(): void
    {
        $company = Company::create([
            'name' => 'Pending Company',
            'slug' => 'pending-company',
            'industry' => 'Technology',
            'status' => 'pending',
            'contact_name' => 'Company Contact',
            'email' => 'company@example.com',
            'password' => 'SecurePass123',
            'account_status' => 'active',
        ]);

        $this->post(route('login.submit'), [
            'email' => 'company@example.com',
            'password' => 'SecurePass123',
        ])->assertRedirect(route('employer.dashboard'));
        $this->assertAuthenticatedAs($company, 'company');

        $this->get(route('home'))->assertRedirect(route('employer.dashboard'));
        $this->get(route('employer.dashboard'))
            ->assertOk()
            ->assertSee('Awaiting verification')
            ->assertSee('Pending Company');
    }

    public function test_admin_can_verify_a_company_and_unlock_employer_tools(): void
    {
        $admin = Admin::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => 'SecurePass123']);
        $company = Company::create([
            'name' => 'Verification Example',
            'slug' => 'verification-example',
            'industry' => 'Technology',
            'status' => 'pending',
            'contact_name' => 'Company Contact',
            'email' => 'company@example.com',
            'password' => 'SecurePass123',
            'account_status' => 'active',
        ]);

        $this->actingAs($company, 'company')
            ->get(route('admin.companies'))
            ->assertRedirect(route('login'));

        $this->actingAs($admin, 'admin')
            ->get(route('admin.companies', ['status' => 'Pending']))
            ->assertOk()
            ->assertSee('Verification Example')
            ->assertSee('Verify')
            ->assertSee('Reject');

        $this->post(route('admin.companies.verify', $company))
            ->assertRedirect(route('admin.companies'));

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'status' => 'verified',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.companies.show', $company))
            ->assertOk()
            ->assertSee('Verification Example')
            ->assertSee('Company Contact')
            ->assertSee('company@example.com')
            ->assertSee('Verified');

        $company->refresh();
        $this->actingAs($company, 'company')
            ->get(route('employer.dashboard'))
            ->assertOk()
            ->assertSee('Hello,')
            ->assertSee('Verification Example')
            ->assertDontSee('Awaiting verification');

        $this->get(route('employer.jobs'))
            ->assertOk()
            ->assertSee('No jobs yet')
            ->assertSee('Post a job');
    }

    public function test_admin_can_reject_a_pending_company(): void
    {
        $admin = Admin::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => 'SecurePass123']);
        $company = Company::create([
            'name' => 'Rejected Example',
            'slug' => 'rejected-example',
            'industry' => 'Technology',
            'status' => 'pending',
            'contact_name' => 'Company Contact',
            'email' => 'company@example.com',
            'password' => 'SecurePass123',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.companies.reject', $company))
            ->assertRedirect(route('admin.companies'))
            ->assertSessionHas('status', 'Rejected Example has been rejected.');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'status' => 'rejected',
        ]);

        $company->refresh();
        $this->actingAs($company, 'company')
            ->get(route('employer.dashboard'))
            ->assertOk()
            ->assertSee('Verification not approved')
            ->assertSee('Rejected Example');
        $this->get(route('employer.jobs'))->assertRedirect(route('employer.dashboard'));
    }

    public function test_invalid_login_uses_a_generic_error(): void
    {
        $this->post(route('login.submit'), [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors([
            'email' => 'These details do not match our records.',
        ]);

        $this->assertGuest();
    }

    public function test_login_throttle_is_per_email_and_ip(): void
    {
        $candidate = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => Hash::make('SecurePass123'),
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.submit'), [
                'email' => 'wrong@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.submit'), [
            'email' => 'wrong@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);

        $this->post(route('login.submit'), [
            'email' => 'candidate@example.com',
            'password' => 'SecurePass123',
        ])->assertRedirect(route('candidate.onboarding'));

        $this->assertAuthenticatedAs($candidate);
    }
}
