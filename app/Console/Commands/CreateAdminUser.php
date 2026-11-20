<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    protected $signature = 'employra:admin:create {email?}';

    protected $description = 'Create an administrator account for Employra';

    public function handle(): int
    {
        $email = Str::lower((string) ($this->argument('email') ?: $this->ask('Admin email')));
        if (User::where('email', $email)->exists()
            || Company::where('email', $email)->exists()
            || Admin::where('email', $email)->exists()) {
            $this->error('That email already belongs to an account. Use a separate email for the administrator account.');

            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Admin name'));
        $password = (string) $this->secret('Password');
        $passwordConfirmation = (string) $this->secret('Confirm password');
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'status' => 'active',
        ]);

        $this->info('Administrator account created. Sign in through the Employra login page.');

        return self::SUCCESS;
    }
}
