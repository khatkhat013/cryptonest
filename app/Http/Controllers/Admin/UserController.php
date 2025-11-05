<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'User status updated successfully');
    }

    /**
     * Toggle admin-enforced forced-loss flag for a user.
     * Only admins that can manage the user should be able to call this (routes/view already guard access).
     */
    public function toggleForceLoss(User $user)
    {
        $user->force_loss = !$user->force_loss;
        $user->save();

        $label = $user->force_loss ? 'enabled' : 'disabled';
        return back()->with('success', "Force-loss has been {$label} for user {$user->user_id}");
    }
}
