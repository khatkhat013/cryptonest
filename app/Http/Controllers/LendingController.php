<?php

namespace App\Http\Controllers;

use App\Models\LendingApplication;
use Illuminate\Http\Request;

class LendingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'borrowing_amount' => 'required|numeric|min:0',
            'credit_period' => 'required|integer|in:10,12,15,20',
            'housing_info' => 'required|file|image|max:2048',
            'income_proof' => 'required|file|image|max:2048',
            'bank_details' => 'required|file|image|max:2048',
            'identity_proof' => 'required|file|image|max:2048',
        ]);

        // Store files in storage/app/public/lending_docs
        $housingInfoPath = $request->file('housing_info')->store('public/lending_docs');
        $incomeProofPath = $request->file('income_proof')->store('public/lending_docs');
        $bankDetailsPath = $request->file('bank_details')->store('public/lending_docs');
        $identityProofPath = $request->file('identity_proof')->store('public/lending_docs');

        // Create lending application
        $lendingApplication = LendingApplication::create([
            'user_id' => auth()->id(),
            'borrowing_amount' => $request->borrowing_amount,
            'credit_period' => $request->credit_period,
            'housing_info_path' => $housingInfoPath,
            'income_proof_path' => $incomeProofPath,
            'bank_details_path' => $bankDetailsPath,
            'identity_proof_path' => $identityProofPath,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lending application submitted successfully',
            'data' => $lendingApplication
        ]);
    }

    // Return lending applications for the current user (JSON)
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $user = $request->user();

        $applications = LendingApplication::where('user_id', $user->id)
            ->when($status, function ($q, $status) {
                if ($status === 'complete') {
                    return $q->where('status', 'approved');
                }
                return $q->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }
}