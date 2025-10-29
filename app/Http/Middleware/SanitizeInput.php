<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Basic sanitization: trim and strip tags. Do not double-encode entities here.
                $value = trim(strip_tags($value));
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
