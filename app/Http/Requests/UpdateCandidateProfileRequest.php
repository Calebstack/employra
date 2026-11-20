<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\NigerianStates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCandidateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'candidate';
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'headline' => ['nullable', 'string', 'max:160'],
            'location' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'portfolio_url' => ['nullable', 'url:http,https', 'max:255'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'availability' => ['required', Rule::in(['immediately', 'within_2_weeks', 'within_1_month', 'flexible'])],
            'preferred_locations' => ['nullable', 'array', 'max:5'],
            'preferred_locations.*' => ['nullable', 'string', Rule::in(array_keys(NigerianStates::options())), 'distinct:ignore_case'],
            'expected_salary_min' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'expected_salary_max' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=160,min_height=160'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $minimum = $this->input('expected_salary_min');
            $maximum = $this->input('expected_salary_max');

            if (is_numeric($minimum) && is_numeric($maximum) && (int) $minimum > (int) $maximum) {
                $validator->errors()->add('expected_salary_max', 'The maximum salary must be at least the minimum salary.');
            }
        });
    }
}
