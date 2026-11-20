<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    protected $table = 'employer_jobs';

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'category',
        'experience_level',
        'job_type',
        'work_mode',
        'location',
        'salary_min',
        'salary_max',
        'deadline',
        'summary',
        'responsibilities',
        'benefits',
        'required_skills',
        'preferred_skills',
        'min_experience_years',
        'minimum_education',
        'notify_applicants',
        'status',
        'moderation_note',
        'moderated_by',
        'moderated_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'required_skills' => 'array',
            'preferred_skills' => 'array',
            'notify_applicants' => 'boolean',
            'moderated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'target_id')->where('target_type', 'job');
    }
}
