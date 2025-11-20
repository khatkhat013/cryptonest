<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminApprovalRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin && !$admin->isApproved()) {
            // Admin not approved - redirect or return error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your admin account is pending approval from the site owner. Please wait for approval before accessing this feature.',
                    'status' => $admin->isPending() ? 'pending' : 'rejected',
                    'reason' => $admin->rejection_reason
                ], 403);
            }

            return redirect()->route('admin.dashboard')
                ->with('warning', 'Your account is pending approval. You cannot edit records until approved.');
        }

        return $next($request);
    }
}
