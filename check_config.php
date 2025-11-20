<?php
// Quick config check
require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "\n=== CONFIG CHECK ===\n";
echo "Bot Token: " . substr(config('services.telegram.bot_token'), 0, 20) . "...\n";
echo "Channel ID: " . config('services.telegram.channel_id') . "\n";
echo "From env: " . env('TELEGRAM_CHANNEL_ID') . "\n";
echo "\n";
?>
