<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    // Role id mapping (match roles seeded in RoleSeeder)
    protected $superRole = 2; // super admin role_id
    protected $adminRole = 1; // normal admin role_id

    protected $fillable = [
        'name',
        'email', 
        'phone',
        'password',
        'telegram_username',
        'role_id',
        'is_approved',
        'rejection_reason',
        'approved_at',
        'approved_by'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'assigned_admin_id');
    }

    public function wallet()
    {
        return $this->hasOne(AdminWallet::class);
    }

    /**
     * Check if admin has the given role
     */
    public function hasRole(string $role): bool 
    {
        return $this->role?->name === $role;
    }

    /**
     * Check if admin is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'super';
    }

    /**
     * Check if admin is normal admin
     */
    public function isNormalAdmin(): bool
    {
        // RoleSeeder uses the name 'admin' for regular admins
        return $this->role?->name === 'admin';
    }

    /**
     * Check if admin is approved by site owner
     */
    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    /**
     * Check if admin approval is pending
     */
    public function isPending(): bool
    {
        return !$this->is_approved && is_null($this->rejection_reason);
    }

    /**
     * Check if admin was rejected
     */
    public function isRejected(): bool
    {
        return !$this->is_approved && !is_null($this->rejection_reason);
    }

    /**
     * Check if admin can manage user
     */
    public function canManageUser(User $user): bool
    {
        return $this->isSuperAdmin() || $user->assigned_admin_id === $this->id;
    }
}