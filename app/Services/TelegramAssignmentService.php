<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;

class TelegramAssignmentService
{
    /**
     * User ကို Admin ချိတ်ဆက်ခြင်း
     */
    public function assignUserToAdminByTelegram(string $uid, string $telegramUsername): array
    {
        try {
            // Clean up telegram username - remove @ if present
            $telegramUsername = ltrim($telegramUsername, '@');

            // User ကို UID မှ ရှာခြင်း
            $user = User::where('user_id', $uid)->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => "❌ User UID မသတ်မှတ်ရှိခြင်း: $uid"
                ];
            }

            // Admin ကို telegram_username မှ ရှာခြင်း
            $admin = Admin::where('telegram_username', $telegramUsername)->first();
            
            if (!$admin) {
                return [
                    'success' => false,
                    'message' => "❌ Admin username မသတ်မှတ်ရှိခြင်း: @$telegramUsername"
                ];
            }

            // User ကို admin ချိတ်ဆက်ခြင်း
            $user->update([
                'assigned_admin_id' => $admin->id,
                'assigned_admin_date' => now()
            ]);

            Log::info('User assigned to admin', [
                'user_id' => $user->user_id,
                'user_email' => $user->email,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'timestamp' => now()
            ]);

            return [
                'success' => true,
                'message' => "✅ User ကို Admin ချိတ်ဆက်သည်",
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => $user->user_id
                ],
                'admin' => [
                    'name' => $admin->name,
                    'telegram_username' => $admin->telegram_username
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Assignment error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => "❌ အမှားအရာ: " . $e->getMessage()
            ];
        }
    }

    /**
     * User အား Admin မှ ဖြုတ်ခြင်း
     */
    public function unassignUserFromAdmin(string $uid): array
    {
        try {
            $user = User::where('user_id', $uid)->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => "❌ User UID မသတ်မှတ်ရှိခြင်း: $uid"
                ];
            }

            if (!$user->assigned_admin_id) {
                return [
                    'success' => false,
                    'message' => "❌ User သည် admin ချိတ်ဆက်ထားခြင်းမရှိခြင်း"
                ];
            }

            $adminName = $user->admin?->name ?? 'Unknown';
            
            $user->update([
                'assigned_admin_id' => null,
                'assigned_admin_date' => null
            ]);

            Log::info('User unassigned from admin', [
                'user_id' => $user->user_id,
                'previous_admin' => $adminName
            ]);

            return [
                'success' => true,
                'message' => "✅ User အား admin မှ ဖြုတ်သည်",
                'user' => [
                    'name' => $user->name,
                    'user_id' => $user->user_id
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Unassignment error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => "❌ အမှားအရာ: " . $e->getMessage()
            ];
        }
    }

    /**
     * Admin အားအားလုံးကို ရယူခြင်း
     */
    public function getAllAdmins(): array
    {
        return Admin::select('id', 'name', 'telegram_username')
            ->get()
            ->toArray();
    }

    /**
     * User assignment အခြေအနေကို ရယူခြင်း
     */
    public function getUserAssignmentStatus(string $uid): array
    {
        $user = User::where('user_id', $uid)->first();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => "User မသတ်မှတ်ရှိခြင်း"
            ];
        }

        return [
            'success' => true,
            'user' => [
                'name' => $user->name,
                'user_id' => $user->user_id,
                'email' => $user->email
            ],
            'assigned_admin' => $user->admin ? [
                'name' => $user->admin->name,
                'telegram_username' => $user->admin->telegram_username,
                'assigned_date' => $user->assigned_admin_date?->format('Y-m-d H:i:s')
            ] : null
        ];
    }
}
