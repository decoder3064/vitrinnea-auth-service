<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'country',
        'allowed_countries',
        'active',
        'email_verified_at',
        'external_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
        'allowed_countries' => 'array',
    ];

    /**
     * Temporary property to store selected country for JWT
     */
    public ?string $selectedCountry = null;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'user_type' => $this->user_type,
            'country' => $this->selectedCountry ?? $this->country,
            'allowed_countries' => $this->allowed_countries ?? [$this->country],
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'groups' => $this->groups->pluck('name'),
        ];
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'user_groups')
            ->withTimestamps();
    }

    /**
     * Check if user has access to a specific country
     */
    public function hasAccessToCountry(string $countryCode): bool
    {
        if (empty($this->allowed_countries)) {
            return false;
        }

        return in_array(strtoupper($countryCode), array_map('strtoupper', $this->allowed_countries));
    }
}