<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class CompanyPresenter
{
    public static function forPublic(Company $company): array
    {
        return [
            'id' => $company->id,
            'slug' => $company->slug,
            'name' => $company->name,
            'initials' => JobPresenter::initials($company->name),
            'color' => '#1558d6',
            'logo_url' => $company->logo_path ? Storage::disk('public')->url($company->logo_path) : null,
            'industry' => $company->industry,
            'location' => $company->location ?? '',
            'size' => $company->company_size ?? '',
            'founded' => '',
            'website' => $company->website ?? '',
            'short' => \Illuminate\Support\Str::limit($company->description ?? '', 120),
            'about' => $company->description ?? 'Company information has not been provided.',
            'open_roles' => $company->jobs_count ?? 0,
        ];
    }
}
