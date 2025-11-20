<?php
/**
 * Direct Telegram Bot Test
 * Usage: php test_telegram_direct.php
 */

// Direct API test without Laravel overhead
$botToken = '8426503372:AAEGNx3nuaAX4-8zaQ-Rg4RUO4PkRHl39ZA';
$chatId = '-5040335752';

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║         TELEGRAM BOT DIRECT TEST                          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "🔹 Bot Token: " . substr($botToken, 0, 20) . "...\n";
echo "🔹 Chat ID: $chatId\n";
echo "🔹 Testing API connection...\n\n";

// Step 1: Test getMe
echo "📍 Step 1: Getting bot info...\n";
$url = "https://api.telegram.org/bot{$botToken}/getMe";
$response = @file_get_contents($url);

if ($response === false) {
    echo "❌ Failed to connect to Telegram API\n";
    die();
}

$result = json_decode($response, true);

if ($result['ok']) {
    echo "✅ Bot info retrieved:\n";
    echo "   - Bot ID: " . $result['result']['id'] . "\n";
    echo "   - Bot Username: @" . $result['result']['username'] . "\n";
    echo "   - First Name: " . $result['result']['first_name'] . "\n\n";
} else {
    echo "❌ Bot API Error: " . $result['description'] . "\n";
    die();
}

// Step 2: Send test message
echo "📍 Step 2: Sending test message to chat...\n";

$testMessage = "🤖 CryptoNest Telegram Bot Test\n\n" .
               "✅ Bot successfully connected!\n" .
               "📅 Test Time: " . date('Y-m-d H:i:s') . "\n" .
               "🎯 Chat ID: " . $chatId;

$sendUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

$postData = http_build_query([
    'chat_id' => $chatId,
    'text' => $testMessage,
    'parse_mode' => 'HTML'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => $postData,
        'timeout' => 10
    ],
    'https' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => $postData,
        'timeout' => 10
    ]
]);

$response = @file_get_contents($sendUrl, false, $context);

if ($response === false) {
    echo "❌ Failed to send message - Connection error\n";
    echo "💡 Check if your internet connection is working\n";
    die();
}

$result = json_decode($response, true);

echo "\n📋 API Response:\n";
echo "   Status: " . ($result['ok'] ? "✅ OK" : "❌ FAILED") . "\n";

if ($result['ok']) {
    echo "   Message ID: " . $result['result']['message_id'] . "\n";
    echo "   Chat ID: " . $result['result']['chat']['id'] . "\n";
    echo "   Sent At: " . date('Y-m-d H:i:s', $result['result']['date']) . "\n\n";
    echo "🎉 SUCCESS! Message sent to Telegram!\n";
    echo "📍 Check your Telegram group for the test message.\n\n";
} else {
    echo "   Error Code: " . $result['error_code'] . "\n";
    echo "   Error Message: " . $result['description'] . "\n\n";
    echo "❌ FAILED! " . $result['description'] . "\n";
    
    // Provide troubleshooting tips
    echo "\n💡 Troubleshooting Tips:\n";
    if (strpos($result['description'], 'chat not found') !== false) {
        echo "   • Chat ID might be incorrect\n";
        echo "   • Bot might not be in the group\n";
        echo "   • Try creating a new group and adding bot with /start\n";
    } elseif (strpos($result['description'], 'FORBIDDEN') !== false) {
        echo "   • Bot doesn't have permission to send messages\n";
        echo "   • Make bot an Admin in the group\n";
        echo "   • Grant 'Send Messages' permission to bot\n";
    }
}

echo "\n";
?>
