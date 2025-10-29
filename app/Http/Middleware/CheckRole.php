<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user('admin')?->role?->name === $role) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized. ' . ucfirst($role) . ' access required.'], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized. ' . ucfirst($role) . ' access required.');
        }

        return $next($request);
    }
}
