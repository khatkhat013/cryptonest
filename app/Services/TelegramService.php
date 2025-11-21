<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Send message to Telegram channel
     */
    public static function sendMessage($chatId, $text, $parseMode = 'HTML')
    {
        try {
            $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            
            if (!$botToken || !$chatId) {
                Log::error('Telegram: Missing bot token or chat ID', [
                    'has_token' => !empty($botToken),
                    'has_chat_id' => !empty($chatId),
                ]);
                return ['success' => false, 'error' => 'Missing credentials'];
            }

            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            
            Log::info('Sending Telegram message', [
                'url' => $url,
                'chat_id' => $chatId,
                'parse_mode' => $parseMode,
                'text' => substr($text, 0, 100) . '...',
            ]);

            $response = Http::timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);

            Log::info('Telegram response received', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                Log::info('✅ Telegram message sent successfully', [
                    'chat_id' => $chatId,
                    'message_id' => optional($response->json())['result']['message_id'] ?? null
                ]);
                return ['success' => true, 'data' => $response->json()];
            } else {
                Log::error('❌ Telegram API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'chat_id' => $chatId,
                ]);
                return ['success' => false, 'error' => $response->body()];
            }
        } catch (\Exception $e) {
            Log::error('❌ Telegram exception: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'chat_id' => $chatId,
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Format plan inquiry message with admin and plan details
     * 
     * @param string $adminName
     * @param string $adminPhone
     * @param string $adminTelegram
     * @param string $planName
     * @param string $planPrice
     * @param string $planDescription
     * @param string $planPriceUsd
     * @return string
     */
    public static function formatPlanInquiryMessage($adminName, $adminPhone, $adminTelegram, $planName, $planPrice, $planDescription, $planPriceUsd = '0')
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        
        $message = "📌 Admin Plan Inquiry\n\n";
        $message .= "👤 Admin: {$adminName}\n";
        $message .= "📱 Phone: {$adminPhone}\n";
        
        if (!empty($adminTelegram)) {
            $message .= "💬 Telegram: @{$adminTelegram}\n";
        }
        
        $message .= "\n💼 Plan Details:\n";
        $message .= "Plan Name: {$planName}\n";
        $message .= "Price: {$planPrice} MMK / {$planPriceUsd} USDT\n";
        
        if (!empty($planDescription)) {
            $message .= "Description: {$planDescription}\n";
        }
        
        $message .= "\n🕒 Timestamp: {$timestamp}";

        return $message;
    }

    /**
     * Verify bot is in the group and has send message permission
     * 
     * @param string $chatId
     * @return array
     */
    public static function verifyBotAccess($chatId)
    {
        try {
            $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            
            if (!$botToken || !$chatId) {
                return ['success' => false, 'message' => 'Missing credentials'];
            }

            // Get bot info
            $url = "https://api.telegram.org/bot{$botToken}/getMe";
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                Log::error('❌ Failed to verify bot', ['response' => $response->body()]);
                return ['success' => false, 'message' => 'Bot verification failed'];
            }

            $botInfo = $response->json();
            Log::info('✅ Bot verified', ['bot_id' => $botInfo['result']['id'] ?? null]);

            // Try sending a test message
            $testUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $testResponse = Http::timeout(10)->post($testUrl, [
                'chat_id' => $chatId,
                'text' => '🤖 Bot connection test - CryptoNest System',
                'parse_mode' => 'HTML',
            ]);

            if ($testResponse->successful()) {
                Log::info('✅ Bot can send messages to group', [
                    'chat_id' => $chatId,
                    'message_id' => optional($testResponse->json())['result']['message_id'] ?? null
                ]);
                return ['success' => true, 'message' => 'Bot access verified'];
            } else {
                Log::error('❌ Bot cannot send messages', [
                    'chat_id' => $chatId,
                    'error' => $testResponse->json('description'),
                ]);
                return ['success' => false, 'message' => 'Bot access denied: ' . $testResponse->json('description')];
            }
        } catch (\Exception $e) {
            Log::error('❌ Bot verification exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get list of recent group chats where bot is active
     * 
     * @return array
     */
    public static function getGroupUpdates()
    {
        try {
            $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            
            if (!$botToken) {
                return ['success' => false, 'groups' => []];
            }

            $url = "https://api.telegram.org/bot{$botToken}/getUpdates";
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                return ['success' => false, 'groups' => []];
            }

            $updates = $response->json('result');
            $groups = [];

            foreach ($updates as $update) {
                if (isset($update['message']['chat'])) {
                    $chat = $update['message']['chat'];
                    if (in_array($chat['type'], ['group', 'supergroup'])) {
                        $chatId = $chat['id'];
                        if (!array_key_exists($chatId, $groups)) {
                            $groups[$chatId] = [
                                'id' => $chatId,
                                'title' => $chat['title'] ?? 'Unknown',
                                'type' => $chat['type'],
                            ];
                        }
                    }
                }
            }

            Log::info('📊 Active groups found', ['count' => count($groups)]);

            return ['success' => true, 'groups' => array_values($groups)];
        } catch (\Exception $e) {
            Log::error('❌ Failed to get group updates', ['error' => $e->getMessage()]);
            return ['success' => false, 'groups' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a photo to the Telegram channel (can be remote URL)
     */
    public static function sendPhoto($chatId, $photoUrl, $caption = '', $parseMode = 'HTML')
    {
        try {
            $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            if (!$botToken || !$chatId || !$photoUrl) {
                return ['success' => false, 'error' => 'Missing credentials or photo'];
            }

            $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
            $response = Http::timeout(15)->post($url, [
                'chat_id' => $chatId,
                'photo' => $photoUrl,
                'caption' => $caption,
                'parse_mode' => $parseMode,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            Log::error('❌ Telegram sendPhoto exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send multiple photos as an album (media group)
     * @param string $chatId
     * @param array $media - array of ['type' => 'photo', 'media' => $url, 'caption' => 'optional']
     */
    public static function sendMediaGroup($chatId, array $media)
    {
        try {
            $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            if (!$botToken || !$chatId || empty($media)) {
                return ['success' => false, 'error' => 'Missing credentials or media'];
            }

            $url = "https://api.telegram.org/bot{$botToken}/sendMediaGroup";
            $response = Http::timeout(15)->post($url, [
                'chat_id' => $chatId,
                'media' => json_encode($media),
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            Log::error('❌ Telegram sendMediaGroup exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
