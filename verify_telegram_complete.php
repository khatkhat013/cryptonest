#!/usr/bin/env php
<?php
/**
 * Complete Telegram Bot Verification & Setup
 * Usage: php verify_telegram_complete.php
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TELEGRAM BOT COMPLETE VERIFICATION & SETUP               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Step 1: Check Bot Token
echo "Step 1: Checking Bot Token\n";
echo "─────────────────────────────────────────────────────────────\n";

$botToken = '8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA';

echo "Bot Token: " . substr($botToken, 0, 20) . "...\n";
echo "Token Format Check: ";

if (preg_match('/^\d+:[A-Za-z0-9_-]+$/', $botToken)) {
    echo "OK (Valid format)\n\n";
} else {
    echo "ERROR (Invalid format)\n";
    echo "Expected: numeric_id:token_string\n\n";
    die();
}

// Step 2: Test Bot API Connection
echo "Step 2: Testing Bot API Connection\n";
echo "─────────────────────────────────────────────────────────────\n";

$url = "https://api.telegram.org/bot{$botToken}/getMe";
echo "URL: $url\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "CURL Error: $curlError\n\n";
} else {
    echo "HTTP Code: $httpCode\n";
    $data = json_decode($response, true);
    
    if ($data['ok']) {
        echo "Status: OK\n";
        echo "Bot ID: " . $data['result']['id'] . "\n";
        echo "Bot Username: @" . $data['result']['username'] . "\n";
        echo "Bot Name: " . $data['result']['first_name'] . "\n\n";
    } else {
        echo "Status: ERROR\n";
        echo "Error: " . $data['description'] . "\n\n";
        echo "SOLUTIONS:\n";
        echo "1. Check bot token is correct\n";
        echo "2. Check internet connection\n";
        echo "3. Try getting new bot token from @BotFather\n\n";
        die();
    }
}

// Step 3: Get Recent Updates
echo "Step 3: Checking Bot Updates & Messages\n";
echo "─────────────────────────────────────────────────────────────\n";

$updateUrl = "https://api.telegram.org/bot{$botToken}/getUpdates";
echo "Fetching updates...\n";

$ch = curl_init($updateUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

$updates = json_decode($response, true);

if (!$updates['ok']) {
    echo "ERROR: Could not fetch updates\n\n";
} else {
    $result = $updates['result'];
    echo "Total Updates: " . count($result) . "\n\n";
    
    if (empty($result)) {
        echo "No updates found. This could mean:\n";
        echo "1. No messages sent to bot yet\n";
        echo "2. Bot token might be wrong\n";
        echo "3. Bot might be inactive\n\n";
        
        echo "ACTION REQUIRED:\n";
        echo "Send /start to bot in Telegram before running this script\n\n";
    } else {
        echo "Recent Activity:\n";
        echo "─────────────────────────────────────────────────────────────\n";
        
        $chatIds = array();
        $userIds = array();
        
        foreach ($result as $idx => $update) {
            if (isset($update['message'])) {
                $msg = $update['message'];
                $chatId = $msg['chat']['id'];
                $chatType = $msg['chat']['type'];
                $userId = $msg['from']['id'];
                $text = $msg['text'] ?? '(no text)';
                
                if (!in_array($chatId, $chatIds)) {
                    $chatIds[] = $chatId;
                }
                if (!in_array($userId, $userIds)) {
                    $userIds[] = $userId;
                }
                
                echo "\nUpdate #" . ($idx + 1) . ":\n";
                echo "  Chat ID: $chatId\n";
                echo "  Chat Type: $chatType\n";
                echo "  User ID: $userId\n";
                echo "  Text: $text\n";
            }
        }
        
        if (!empty($chatIds)) {
            echo "\n\nChat IDs Found:\n";
            foreach ($chatIds as $id) {
                echo "  - $id\n";
            }
        }
    }
}

// Step 4: Instructions
echo "\n\nStep 4: Next Actions\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "1. Send /start to @Cryptonest_support_bot in Telegram\n";
echo "2. Run this script again: php verify_telegram_complete.php\n";
echo "3. Copy the Chat ID shown above\n";
echo "4. Update .env file:\n";
echo "   TELEGRAM_CHANNEL_ID=[chat_id_from_above]\n";
echo "5. Run: php artisan config:clear && php artisan config:cache\n";
echo "6. Test landing page contact button\n\n";

echo "Note: If you created a group, send /start in the group\n";
echo "If you messaged bot directly, send /start in DM\n\n";

?>
