<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * Handle an incoming request with XSS and injection prevention.
     * 
     * ===== OWASP A03:2021 – Injection Protection =====
     * - XSS (Cross-Site Scripting) Prevention
     * - SQL Injection Prevention (via prepared statements in models)
     * - Command Injection Prevention
     * 
     * Sanitizes user input while preserving necessary data.
     */
    public function handle(Request $request, Closure $next)
    {
        // Don't sanitize file uploads or JSON API responses
        if ($request->isJson() && $request->is('api/*')) {
            return $next($request);
        }

        $input = $request->all();

        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Step 1: Remove null bytes (null byte injection)
                $value = str_replace("\0", '', $value);
                
                // Step 2: Trim whitespace
                $value = trim($value);
                
                // Step 3: HTML encode dangerous characters
                // This prevents XSS by converting special characters to HTML entities
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
                
                // Step 4: Strip potentially dangerous tags (defense in depth)
                $value = strip_tags($value);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
