<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The admins that have this role.
     */
    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    /**
     * Check if role is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->name === 'super';
    }

    /**
     * Check if role is normal admin
     */
    public function isNormalAdmin(): bool
    {
        return $this->name === 'normal';
    }

    /**
     * Get role display name
     */
    public function getDisplayName(): string
    {
        return match ($this->name) {
            'super' => 'Super Admin',
            'normal' => 'Admin',
            default => ucfirst($this->name),
        };
    }
}