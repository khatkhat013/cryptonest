<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use App\Models\AdminWallet;
use App\Models\ActionStatus;
use Illuminate\Support\Facades\Storage;

class DepositController extends Controller
{
    public function store(StoreDepositRequest $request)
    {
        // Validated data is automatically escaped and filtered via FormRequest
        $validated = $request->validated();

        $user = $request->user();

        // find assigned admin id if present
        $adminId = $user->assigned_admin_id ?? null;

        $imagePath = null;
        if ($request->hasFile('image')) {
            // File is validated by FormRequest - store safely
            $imagePath = $request->file('image')->store('deposits', 'public');
        }

        // determine 'pending' action status id (fall back to creating it if missing)
        $pendingStatus = ActionStatus::firstOrCreate(['name' => 'pending']);

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'admin_id' => $adminId,
            'coin' => strtolower($validated['coin']), // Already validated in FormRequest
            'sent_address' => $validated['sent_address'] ?? null,
            'amount' => $validated['amount'],
            'image_path' => $imagePath,
            'action_status_id' => $pendingStatus->id
        ]);

        return back()->with('success', 'Deposit submitted successfully.');
    }
}
