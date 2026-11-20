<?php

namespace App\Support;

use App\Models\CandidateProfile;
use App\Models\User;

class CandidateProfilePresenter
{
    public static function forCandidate(User $user, CandidateProfile $profile): array
    {
        $nameParts = preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $salary = $profile->expected_salary_min === null && $profile->expected_salary_max === null
            ? 'Not provided'
            : 'NGN '.($profile->expected_salary_min === null ? 'Not specified' : number_format($profile->expected_salary_min))
                .' - NGN '.($profile->expected_salary_max === null ? 'Not specified' : number_format($profile->expected_salary_max)).' / month';

        return [
            'name' => $user->name,
            'initials' => JobPresenter::initials($user->name),
            'first_name' => $nameParts[0] ?? '',
            'last_name' => implode(' ', array_slice($nameParts, 1)),
            'email' => $user->email,
            'phone' => $profile->phone ?? '',
            'headline' => $profile->headline ?? '',
            'location' => $profile->location ?? '',
            'portfolio_url' => $profile->portfolio_url ?? '',
            'summary' => $profile->summary ?? '',
            'photo_path' => $profile->photo_path,
            'photo_url' => $profile->photo_path ? route('candidate.profile.photo') : null,
            'preferred_roles' => implode(', ', $profile->preferred_roles ?? []),
            'preferred_locations' => implode(', ', $profile->preferred_locations ?? []),
            'preferred_work_modes' => implode(', ', $profile->preferred_work_modes ?? []),
            'preferred_locations_values' => $profile->preferred_locations ?? [],
            'expected_salary_min' => $profile->expected_salary_min,
            'expected_salary_max' => $profile->expected_salary_max,
            'expected_salary' => $salary,
            'availability' => self::availabilityLabel($profile->availability),
            'availability_value' => $profile->availability,
            'visibility' => $profile->visibility,
            'completion' => self::completion($profile),
            'completion_missing' => self::missingFields($profile),
        ];
    }

    public static function completion(CandidateProfile $profile): int
    {
        $fields = [
            filled($profile->headline),
            filled($profile->location),
            filled($profile->phone),
            filled($profile->summary),
            filled($profile->portfolio_url),
            filled($profile->photo_path),
            ! empty($profile->preferred_roles),
            ! empty($profile->preferred_locations),
            ! empty($profile->preferred_work_modes),
            $profile->expected_salary_min !== null,
            filled($profile->availability),
        ];

        return (int) round(count(array_filter($fields)) / count($fields) * 100);
    }

    public static function availabilityLabel(?string $availability): string
    {
        return match ($availability) {
            'immediately' => 'Available immediately',
            'within_2_weeks' => 'Available within 2 weeks',
            'within_1_month' => 'Available within 1 month',
            'flexible' => 'Flexible',
            default => 'Not provided',
        };
    }

    private static function missingFields(CandidateProfile $profile): array
    {
        $fields = [
            'Add a headline' => $profile->headline,
            'Add your location' => $profile->location,
            'Add a short summary' => $profile->summary,
            'Add a profile photo' => $profile->photo_path,
            'Add a portfolio link' => $profile->portfolio_url,
        ];

        return array_keys(array_filter($fields, static fn ($value): bool => blank($value)));
    }
}
