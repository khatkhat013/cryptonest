<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\AdminWallet;
use App\Models\ActionStatus;
use Illuminate\Support\Facades\Storage;

class DepositController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'coin' => 'required|string|max:16',
            'amount' => 'required|numeric|min:0.00000001',
            'image' => 'nullable|image|max:5120', // max 5MB
            'sent_address' => 'nullable|string|max:255'
        ]);

        $user = $request->user();

        // find assigned admin id if present
        $adminId = $user->assigned_admin_id ?? null;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('deposits', 'public');
        }

        // determine 'pending' action status id (fall back to creating it if missing)
        $pendingStatus = ActionStatus::firstOrCreate(['name' => 'pending']);

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'admin_id' => $adminId,
            'coin' => strtolower($request->input('coin')),
            'sent_address' => $request->input('sent_address'),
            'amount' => $request->input('amount'),
            'image_path' => $imagePath,
            'action_status_id' => $pendingStatus->id
        ]);

        return back()->with('success', 'Deposit submitted successfully.');
    }
}
