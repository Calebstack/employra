<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccountCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_account_can_be_created_with_an_interactive_password(): void
    {
        $this->artisan('employra:admin:create', ['email' => 'admin@example.com'])
            ->expectsQuestion('Admin name', 'Employra Admin')
            ->expectsQuestion('Password', 'SecurePass123')
            ->expectsQuestion('Confirm password', 'SecurePass123')
            ->expectsOutputToContain('Administrator account created.')
            ->assertExitCode(0);

        $admin = Admin::where('email', 'admin@example.com')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertSame('active', $admin->status);
        $this->assertNotSame('SecurePass123', $admin->password);
        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
    }

    public function test_admin_command_does_not_change_an_existing_account(): void
    {
        User::factory()->create(['email' => 'existing@example.com', 'role' => 'candidate']);

        $this->artisan('employra:admin:create', ['email' => 'existing@example.com'])
            ->expectsOutputToContain('That email already belongs to an account.')
            ->assertExitCode(1);

        $this->assertDatabaseHas('users', ['email' => 'existing@example.com', 'role' => 'candidate']);
    }
}
