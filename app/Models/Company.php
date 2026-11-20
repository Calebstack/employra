<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Authenticatable
{
    protected $fillable = [
        'name',
        'slug',
        'industry',
        'status',
        'contact_name',
        'email',
        'password',
        'account_status',
        'last_login_at',
        'website',
        'location',
        'company_size',
        'description',
        'registration_number',
        'logo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function getRoleAttribute(): string
    {
        return 'employer';
    }
}
