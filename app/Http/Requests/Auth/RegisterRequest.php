<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $role = $this->input('role') === 'employer' ? 'company' : $this->input('role');
        $this->merge([
            'email' => Str::lower((string) $this->input('email')),
            'role' => $role,
        ]);
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $email = (string) $this->input('email');
            if ($email !== '' && (
                DB::table('users')->where('email', $email)->exists()
                || DB::table('companies')->where('email', $email)->exists()
                || DB::table('admins')->where('email', $email)->exists()
            )) {
                $validator->errors()->add('email', 'This email address is already registered.');
            }
        });
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:190'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:candidate,company'],
            'company_name' => ['required_if:role,company', 'nullable', 'string', 'max:120'],
            'industry' => ['required_if:role,company', 'nullable', 'string', 'max:120'],
            'terms' => ['accepted'],
            'redirect' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
