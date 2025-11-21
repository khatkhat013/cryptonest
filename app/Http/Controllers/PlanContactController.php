<?php

namespace App\Http\Controllers;

use App\Models\PlanPrice;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            // Log the incoming request for debugging
            Log::debug('PlanContactController::contactAdmin request', [
                'admin_id' => $admin->id ?? null,
                'plan_id' => $request->input('plan_id'),
                'payment_method' => $request->input('payment_method'),
                'has_crypto_screenshot' => $request->hasFile('crypto_screenshot'),
                'has_mobile_screenshot' => $request->hasFile('mobile_screenshot')
            ]);
            $validated = $request->validate([
                'plan_id' => 'required|string|max:100',
                'crypto_screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'mobile_screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'payment_method' => 'nullable|string|in:crypto,mobile_money'
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

            // Handle file uploads
            $cryptoScreenshotPath = null;
            $mobileScreenshotPath = null;

            if ($request->hasFile('crypto_screenshot') && $request->file('crypto_screenshot')->isValid()) {
                $file = $request->file('crypto_screenshot');
                $filename = 'crypto_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $cryptoScreenshotPath = Storage::disk('public')->putFileAs('payments/screenshots', $file, $filename);
            }

            if ($request->hasFile('mobile_screenshot') && $request->file('mobile_screenshot')->isValid()) {
                $file = $request->file('mobile_screenshot');
                $filename = 'mobile_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $mobileScreenshotPath = Storage::disk('public')->putFileAs('payments/screenshots', $file, $filename);
            }

            // Save plan selection to plan_prices table
            $record = PlanPrice::create([
                'admin_id' => $admin->id,
                'plan_id' => $planId,
                'plan_name' => $plan['name'],
                'plan_price' => $plan['price'],
                'plan_duration' => $plan['duration'] ?? null,
                'plan_description' => $plan['description'] ?? null,
                'crypto_screenshot' => $cryptoScreenshotPath,
                'mobile_screenshot' => $mobileScreenshotPath,
                'payment_method' => $validated['payment_method'] ?? null,
            ]);
            if (!$record || !$record->id) {
                Log::error('PlanContactController::contactAdmin failed to save record', ['admin_id' => $admin->id, 'input' => $validated]);
                return response()->json(['success' => false, 'message' => 'Failed to save plan inquiry.'], 500);
            }

            // Build Telegram message using the service helper
            $message = TelegramService::formatPlanInquiryMessage(
                $admin->name ?? 'N/A',
                $admin->phone ?? 'N/A',
                $admin->telegram_username ?? '',
                $record->plan_name,
                $record->plan_price,
                $record->plan_description ?? '',
                $this->getUsdAmount($planId)
            );

            $channelId = env('TELEGRAM_CHANNEL_ID') ?: config('services.telegram.channel_id');
            // Send text message first
            $result = TelegramService::sendMessage($channelId, $message, 'HTML');

            // If we have screenshots, try to send them as photos (or media group)
            $photoSendResult = null;
            $photos = [];
            if ($record->crypto_screenshot) {
                $photos[] = asset('storage/' . $record->crypto_screenshot);
            }
            if ($record->mobile_screenshot) {
                $photos[] = asset('storage/' . $record->mobile_screenshot);
            }

            if (count($photos) === 1) {
                $photoSendResult = TelegramService::sendPhoto($channelId, $photos[0], 'Screenshot for plan inquiry #' . $record->id, 'HTML');
                if (!($photoSendResult['success'] ?? false)) {
                    Log::warning('Failed to send telegram photo (single)', ['error' => $photoSendResult['error'] ?? null, 'record_id' => $record->id]);
                } else {
                    Log::info('Sent telegram photo successfully', ['record_id' => $record->id]);
                }
            } elseif (count($photos) > 1) {
                // Build media payload
                $media = [];
                foreach ($photos as $idx => $p) {
                    $media[] = ['type' => 'photo', 'media' => $p, 'caption' => ($idx === 0 ? 'Screenshots for plan inquiry #' . $record->id : '')];
                }
                $photoSendResult = TelegramService::sendMediaGroup($channelId, $media);
                if (!($photoSendResult['success'] ?? false)) {
                    Log::warning('Failed to send telegram media group', ['error' => $photoSendResult['error'] ?? null, 'record_id' => $record->id]);
                } else {
                    Log::info('Sent telegram media group successfully', ['record_id' => $record->id]);
                }
            }

            if ($result['success'] || $photoSendResult['success'] ?? false) {
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

    /**
     * Get USD amount for a plan ID (rate: 250 USDT per 10,000,000 MMK)
     */
    private function getUsdAmount($planId)
    {
        $usdAmounts = [
            'free' => '0',
            'standard' => '250',
            'pro' => '500',
            'advanced' => '750',
            'premium' => '1,250',
            'enterprise' => '2,500',
        ];

        return $usdAmounts[$planId] ?? '0';
    }
}
