<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Log activity in a safe way. If the `activity()` helper exists (spatie/laravel-activitylog), use it.
     * Otherwise fallback to a simple Log::info entry.
     *
     * @param mixed $actor
     * @param mixed $target
     * @param string $message
     * @return void
     */
    public static function log($actor, $target, string $message): void
    {
        try {
            if (function_exists('activity')) {
                activity()
                    ->causedBy($actor)
                    ->performedOn($target)
                    ->log($message);
                return;
            }

            // Fallback logging
            $actorId = is_object($actor) && isset($actor->id) ? $actor->id : $actor;
            $targetId = is_object($target) && isset($target->id) ? $target->id : $target;
            Log::info('ActivityLogger: ' . $message, [
                'actor' => $actorId,
                'target' => $targetId,
            ]);
        } catch (\Throwable $e) {
            // Avoid throwing from logger
            Log::error('ActivityLogger failed: ' . $e->getMessage());
        }
    }
}
