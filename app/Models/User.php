<?php

namespace App\Models;

use Carbon\Carbon;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_id',
        'assigned_admin_id',
    ];

    /**
     * Get the assigned admin for this user.
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    /**
     * Get the user's wallet (one-to-one)
     */
    public function wallet()
    {
        return $this->hasOne(UserWallet::class);
    }

    /**
     * Get deposits belonging to the user
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Generate the next user_id
     */
    public static function generateUserId()
    {
        $lastUser = static::orderBy('user_id', 'desc')->first();
        if (!$lastUser || !$lastUser->user_id) {
            return '000000';
        }
        
        return str_pad((intval($lastUser->user_id) + 1), 6, '0', STR_PAD_LEFT);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Check if user is assigned to an admin
     */
    public function isAssigned()
    {
        return !is_null($this->assigned_admin_id);
    }

    /**
     * Get user's registration date in human readable format
     */
    public function getRegistrationDate()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    /**
     * Get last activity timestamp in human readable format
     */
    public function getLastActivityDate()
    {
        return $this->last_activity_at ? 
            Carbon::parse($this->last_activity_at)->diffForHumans() : 
            'Never';
    }
}
