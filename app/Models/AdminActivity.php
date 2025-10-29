<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'type',
        'description',
        'meta',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    /**
     * Get the admin that performed this activity.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Log an activity
     */
    public static function log(Admin $admin, string $type, string $description = null, array $meta = []): self
    {
        $request = request();
        
        return static::create([
            'admin_id' => $admin->id,
            'type' => $type,
            'description' => $description,
            'meta' => $meta,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }

    /**
     * Get readable activity type
     */
    public function getReadableType(): string
    {
        return match($this->type) {
            'login' => 'Logged in',
            'logout' => 'Logged out',
            'user_create' => 'Created user',
            'user_update' => 'Updated user',
            'user_delete' => 'Deleted user',
            'wallet_create' => 'Created wallet',
            'wallet_update' => 'Updated wallet',
            'transaction_create' => 'Created transaction',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }
}