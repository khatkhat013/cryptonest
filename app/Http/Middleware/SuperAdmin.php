<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->isSuperAdmin()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized. Super Admin access required.'], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized. Super Admin access required.');
        }

        return $next($request);
    }
}