<?php

namespace App\Http\Controllers;

use App\Models\PlanPrice;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanContactController extends Controller
{
    /**
     * Contact admin about a plan
     * POST /api/contact-admin
     */
    public function contactAdmin(Request $request)
    {
        // Require admin authentication (route middleware should enforce this too)
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in as an admin to perform this action.'
            ], 401);
        }

        try {
            $validated = $request->validate([
                'plan_id' => 'required|string|max:100'
            ]);

            $planId = $validated['plan_id'];

            // Plan definitions (name, price, duration, description)
            $plans = [
                'free' => [
                    'name' => 'အစမ်းသုံးခွင့် (Free Trial)',
                    'price' => '0',
                    'duration' => 'Trial',
                    'description' => 'API ချိတ်ဆက်မှု၊ အဆင့်မြင့် ကုန်သည်မှု၊ အကန့်အသတ်မဲ့ ကုန်သည်မှု'
                ],
                'standard' => [
                    'name' => '၁ လ သုံးစွဲခွင့် (Standard)',
                    'price' => '1,000,000',
                    'duration' => '1 month',
                    'description' => 'API ချိတ်ဆက်မှု၊ အဆင့်မြင့် ကုန်သည်မှု၊ အသုံးပြုသူ အကူအညီ'
                ],
                'pro' => [
                    'name' => '၂ လ သုံးစွဲခွင့် (Pro)',
                    'price' => '2,000,000',
                    'duration' => '2 months',
                    'description' => 'API ချိတ်ဆက်မှု၊ အဆင့်မြင့် ကုန်သည်မှု၊ +7 days bonus'
                ],
                'advanced' => [
                    'name' => '၃ လ သုံးစွဲခွင့် (Advanced)',
                    'price' => '3,000,000',
                    'duration' => '3 months',
                    'description' => 'API ချိတ်ဆက်မှု၊ အဆင့်မြင့် ကုန်သည်မှု၊ +15 days bonus'
                ],
                'premium' => [
                    'name' => '၅ လ သုံးစွဲခွင့် (Premium)',
                    'price' => '5,000,000',
                    'duration' => '5 months',
                    'description' => 'Full features, 24/7 support'
                ],
                'enterprise' => [
                    'name' => '၁၂ လ သုံးစွဲခွင့် (Enterprise)',
                    'price' => '10,000,000',
                    'duration' => '12 months',
                    'description' => 'Enterprise package with premium support'
                ],
            ];

            if (!isset($plans[$planId])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid plan_id'
                ], 422);
            }

            $plan = $plans[$planId];

            // Save plan selection to plan_prices table
            $record = PlanPrice::create([
                'admin_id' => $admin->id,
                'plan_id' => $planId,
                'plan_name' => $plan['name'],
                'plan_price' => $plan['price'],
                'plan_duration' => $plan['duration'] ?? null,
                'plan_description' => $plan['description'] ?? null,
            ]);

            // Build Telegram message using the service helper
            $message = TelegramService::formatPlanInquiryMessage(
                $admin->name ?? 'N/A',
                $admin->phone ?? 'N/A',
                $admin->telegram_username ?? '',
                $record->plan_name,
                $record->plan_price,
                $record->plan_description ?? ''
            );

            $channelId = env('TELEGRAM_CHANNEL_ID') ?: config('services.telegram.channel_id');
            $result = TelegramService::sendMessage($channelId, $message, 'HTML');

            if ($result['success']) {
                Log::info('Saved plan and sent to Telegram', ['record_id' => $record->id, 'admin_id' => $admin->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Plan inquiry saved and notification sent!',
                    'record_id' => $record->id,
                ]);
            }

            // Telegram sending failed, but record was saved - still return success to user
            Log::warning('Telegram notification failed but plan was saved', [
                'record_id' => $record->id,
                'error' => $result['error'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan inquiry saved successfully! (Notification pending)',
                'record_id' => $record->id,
                'telegram_status' => 'pending',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Plan contact exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal error: ' . $e->getMessage()], 500);
        }
    }
}
