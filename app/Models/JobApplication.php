<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    protected $fillable = [
        'user_id',
        'job_id',
        'cv_id',
        'status',
        'cover_note',
        'availability',
        'expected_salary_min',
        'expected_salary_max',
    ];

    protected function casts(): array
    {
        return [
            'expected_salary_min' => 'integer',
            'expected_salary_max' => 'integer',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(JobApplicationStatusHistory::class, 'job_application_id')->oldest();
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(JobInterview::class, 'job_application_id')->latest();
    }
}
