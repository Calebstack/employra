<?php

namespace App\Support;

use App\Models\Job;
use App\Support\NigerianStates;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobPresenter
{
    public static function forPublic(Job $job): array
    {
        $company = $job->company;
        $skills = array_values(array_unique(array_merge($job->required_skills ?? [], $job->preferred_skills ?? [])));
        $salary = $job->salary_min !== null && $job->salary_max !== null
            ? '₦'.number_format($job->salary_min).' – ₦'.number_format($job->salary_max).' / month'
            : ($job->salary_min !== null ? 'From ₦'.number_format($job->salary_min).' / month' : 'Not specified');

        return [
            'id' => $job->id,
            'is_persisted' => true,
            'slug' => $job->slug,
            'title' => $job->title,
            'company' => $company->name,
            'company_slug' => $company->slug,
            'company_initials' => self::initials($company->name),
            'company_color' => '#1558d6',
            'company_industry' => $company->industry,
            'company_short' => Str::limit($company->description ?? '', 120),
            'company_about' => $company->description ?? '',
            'company_location' => $company->location ?? $job->location,
            'company_size' => $company->company_size ?? '',
            'company_website' => $company->website ?? '',
            'company_logo_url' => $company->logo_path ? Storage::disk('public')->url($company->logo_path) : null,
            'location' => $job->location,
            'state' => NigerianStates::normalize($job->location),
            'mode' => $job->work_mode,
            'type' => $job->job_type,
            'category' => $job->category,
            'level' => $job->experience_level,
            'salary' => $salary,
            'salary_min' => $job->salary_min,
            'salary_max' => $job->salary_max,
            'posted' => $job->created_at?->diffForHumans() ?? '',
            'deadline' => $job->deadline?->format('j M Y') ?? 'Not specified',
            'summary' => $job->summary,
            'responsibilities' => self::lines($job->responsibilities),
            'must' => $job->required_skills ?? [],
            'nice' => $job->preferred_skills ?? [],
            'skills' => $skills,
            'benefits' => self::lines($job->benefits),
            'applicants' => 0,
            'new' => 0,
            'views' => 0,
            'status' => self::statusLabel($job->status),
            'flag' => null,
        ];
    }

    public static function forEmployer(Job $job): array
    {
        return array_merge(self::forPublic($job), [
            'posted' => $job->created_at?->diffForHumans() ?? '',
            'deadline' => $job->deadline?->format('j M Y') ?? 'Not set',
            'applicants' => $job->applications_count ?? $job->applications()->count(),
            'new' => $job->applications()->where('status', 'submitted')->count(),
            'views' => 0,
        ]);
    }

    public static function forAdmin(Job $job): array
    {
        $isFlagged = (bool) $job->has_open_reports;

        return [
            'id' => $job->id,
            'slug' => $job->slug,
            'title' => $job->title,
            'company' => $job->company->name,
            'status' => self::statusLabel($job->status),
            'applicants' => $job->applications_count ?? $job->applications()->count(),
            'posted' => $job->created_at?->diffForHumans() ?? '',
            'flag' => $isFlagged ? 'Open report' : null,
            'owner_email' => $job->company->email ?? '',
        ];
    }

    public static function filter(array $jobs, array $filters): array
    {
        $keyword = Str::lower(trim((string) ($filters['q'] ?? '')));
        $location = Str::lower(trim((string) ($filters['location'] ?? '')));
        $state = NigerianStates::normalize((string) ($filters['state'] ?? ''));
        $salaryMin = isset($filters['salary_min']) && $filters['salary_min'] !== '' ? (int) $filters['salary_min'] : null;
        $salaryMax = isset($filters['salary_max']) && $filters['salary_max'] !== '' ? (int) $filters['salary_max'] : null;
        if ($salaryMin === 0) {
            $salaryMin = null;
        }
        if ($salaryMax === 10000000) {
            $salaryMax = null;
        }

        return array_values(array_filter($jobs, function (array $job) use ($filters, $keyword, $location, $state, $salaryMin, $salaryMax): bool {
            if ($keyword !== '' && ! Str::contains(Str::lower($job['title'].' '.$job['company'].' '.implode(' ', $job['skills']).' '.$job['category']), $keyword)) {
                return false;
            }
            if ($location !== '' && ! Str::contains(Str::lower($job['location'].' '.$job['mode']), $location)) {
                return false;
            }
            if ($state !== null && $job['state'] !== $state && $job['mode'] !== 'Remote') {
                return false;
            }
            if (($salaryMin !== null || $salaryMax !== null)
                && (! is_numeric($job['salary_min']) || ! is_numeric($job['salary_max'])
                    || ($salaryMin !== null && $job['salary_max'] < $salaryMin)
                    || ($salaryMax !== null && $job['salary_min'] > $salaryMax))) {
                return false;
            }

            foreach (['category' => 'category', 'type' => 'type', 'mode' => 'mode', 'level' => 'level'] as $parameter => $key) {
                $value = $filters[$parameter] ?? '';
                if (is_array($value) ? ($value !== [] && ! in_array($job[$key], $value, true)) : ($value !== '' && $job[$key] !== $value)) {
                    return false;
                }
            }

            return true;
        }));
    }

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return Str::upper(substr($parts[0] ?? '', 0, 1).(count($parts) > 1 ? substr($parts[count($parts) - 1], 0, 1) : ''));
    }

    private static function statusLabel(string $status): string
    {
        return match ($status) {
            'open' => 'Open',
            'closed' => 'Closed',
            'taken_down' => 'Taken down',
            default => 'Draft',
        };
    }

    private static function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', $text ?? '') ?: []), fn ($line) => $line !== ''));
    }
}
