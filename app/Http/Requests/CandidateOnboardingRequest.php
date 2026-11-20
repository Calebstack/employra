<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\NigerianStates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CandidateOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'candidate';
    }

    public function rules(): array
    {
        return [
            'preferred_roles' => ['required', 'array', 'min:1', 'max:5'],
            'preferred_roles.*' => ['required', 'string', 'max:160', 'distinct:ignore_case'],
            'preferred_work_modes' => ['required', 'array', 'min:1', 'max:3'],
            'preferred_work_modes.*' => ['required', 'string', 'in:remote,hybrid,on_site', 'distinct'],
            'preferred_locations' => ['required', 'array', 'min:1', 'max:5'],
            'preferred_locations.*' => ['required', 'string', Rule::in(array_keys(NigerianStates::options())), 'distinct:ignore_case'],
            'availability' => ['required', Rule::in(['immediately', 'within_2_weeks', 'within_1_month', 'flexible'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'expected_salary_min' => ['required', 'integer', 'min:0', 'max:10000000'],
            'expected_salary_max' => ['required', 'integer', 'min:0', 'max:10000000'],
            'cv' => [
                'nullable',
                'file',
                'max:5120',
                'mimes:pdf,docx',
                'mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
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
