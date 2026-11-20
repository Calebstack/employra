<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'phone',
        'location',
        'summary',
        'portfolio_url',
        'photo_path',
        'preferred_roles',
        'preferred_work_modes',
        'preferred_locations',
        'expected_salary_min',
        'expected_salary_max',
        'availability',
        'visibility',
        'completion',
        'onboarding_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_roles' => 'array',
            'preferred_work_modes' => 'array',
            'preferred_locations' => 'array',
            'expected_salary_min' => 'integer',
            'expected_salary_max' => 'integer',
            'completion' => 'integer',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class);
    }
}
