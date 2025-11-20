<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Test Telegram service directly
$botToken = config('services.telegram.bot_token');
$channelId = config('services.telegram.channel_id');

echo "Bot Token: " . ($botToken ? substr($botToken, 0, 20) . "..." : "NULL") . "\n";
echo "Channel ID: " . ($channelId ? $channelId : "NULL") . "\n";
echo "\n";

// Test sending a message
$service = new \App\Services\TelegramService();
$message = "🧪 Test message from Telegram Service\n✅ Credentials loaded successfully!";
$result = $service->sendMessage($channelId, $message, 'Markdown');

echo "Result:\n";
echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
