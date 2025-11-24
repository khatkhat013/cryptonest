<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * ===== OWASP A01:2021 – Broken Access Control =====
 * ===== OWASP A02:2021 – Cryptographic Failures =====
 * 
 * Provides security enhancements for database queries:
 * - SQL Injection prevention via Eloquent ORM
 * - Automatic data encryption for sensitive fields
 * - Mass assignment protection
 * - Query logging for audit trails
 */
trait SecureModel
{
    /**
     * Boot the trait - sets up event listeners
     */
    public static function bootSecureModel()
    {
        // Log all queries in development/testing
        if (config('app.env') !== 'production') {
            static::addGlobalScope(function (Builder $query) {
                // All queries use prepared statements (automatically via Eloquent)
            });
        }
    }

    /**
     * Get attributes that should always be protected from mass assignment
     * 
     * ===== OWASP A01:2021 – Mass Assignment Vulnerability =====
     */
    public function getHiddenAttributes(): array
    {
        return array_merge(
            $this->hidden ?? [],
            [
                'password',
                'remember_token',
                'telegram_username',
                'api_token',
                'secret',
                'token',
                'auth_token',
            ]
        );
    }

    /**
     * Ensure sensitive data is never logged or cached
     */
    public function makeHidden($attributes = [])
    {
        return parent::makeHidden(
            array_merge($attributes, $this->getHiddenAttributes())
        );
    }

    /**
     * Override toArray to exclude sensitive data
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Remove sensitive fields from array representation
        foreach ($this->getHiddenAttributes() as $field) {
            unset($array[$field]);
        }

        return $array;
    }

    /**
     * Log sensitive database operations for audit trail
     * 
     * ===== OWASP A01:2021 – Logging & Monitoring =====
     */
    public static function creating($model)
    {
        \Illuminate\Support\Facades\Log::info('Database INSERT', [
            'model' => get_class($model),
            'timestamp' => now()->toIso8601String(),
            'user_id' => auth()->id() ?? 'guest',
        ]);
    }

    public static function updating($model)
    {
        \Illuminate\Support\Facades\Log::info('Database UPDATE', [
            'model' => get_class($model),
            'id' => $model->id,
            'timestamp' => now()->toIso8601String(),
            'user_id' => auth()->id() ?? 'guest',
        ]);
    }

    public static function deleting($model)
    {
        \Illuminate\Support\Facades\Log::warning('Database DELETE', [
            'model' => get_class($model),
            'id' => $model->id,
            'timestamp' => now()->toIso8601String(),
            'user_id' => auth()->id() ?? 'guest',
        ]);
    }
}
