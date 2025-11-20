<?php
/**
 * Script: setup_telegram_group.php
 * Purpose: Automatically fetch and configure Telegram private group ID
 * Usage: php scripts/setup_telegram_group.php
 */

// Load Laravel bootstrap
require __DIR__ . '/../bootstrap/app.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

class TelegramGroupSetup {
    private $botToken;
    private $envPath;
    
    public function __construct() {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->envPath = __DIR__ . '/../.env';
    }
    
    public function run() {
        echo "\n╔════════════════════════════════════════════════════════════╗\n";
        echo "║     TELEGRAM PRIVATE GROUP SETUP FOR CRYPTONEST          ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
        
        if (!$this->botToken) {
            echo "❌ Error: TELEGRAM_BOT_TOKEN not found in .env\n";
            return false;
        }
        
        echo "✅ Bot Token loaded: " . substr($this->botToken, 0, 20) . "...\n\n";
        
        echo "📋 INSTRUCTION:\n";
        echo "─────────────────────────────────────────────────────────────\n";
        echo "1. ဒီ script မှ group ID အားလုံးကို စုဆောင်းပါလိမ့်မယ်\n";
        echo "2. Bot ကို private group တွင်ထည့်ထားပါ\n";
        echo "3. လည်ပင်းအောင်ခုံ Send /start ခလုတ်နှိပ်ပါ\n";
        echo "4. ဒီ command ကိုအပြန်အလှန်ပြန်လည်চလုပ်ပါ\n";
        echo "\n";
        
        echo "🔍 Bot သည် စုံစမ်းနေသည်...\n\n";
        
        // Get updates from bot to find group ID
        $updates = $this->getUpdates();
        
        if (!$updates) {
            echo "⚠️  Updates ရယူမရခြင်း - Manual method အားလုံးကိုစုံစမ်းပါ\n";
            $this->showManualMethod();
            return false;
        }
        
        $groupIds = $this->extractGroupIds($updates);
        
        if (empty($groupIds)) {
            echo "⚠️  Group ID မတွေ့ရှိခြင်း\n";
            echo "\n💡 Manual Method:\n";
            $this->showManualMethod();
            return false;
        }
        
        echo "✅ Found " . count($groupIds) . " group(s):\n\n";
        
        foreach ($groupIds as $index => $id) {
            echo "  " . ($index + 1) . ". -100" . abs($id) . "\n";
        }
        
        echo "\n";
        echo "📝 Choose group number (or enter full ID): ";
        
        $input = trim(fgets(STDIN));
        $selectedId = null;
        
        if (is_numeric($input) && $input > 0 && $input <= count($groupIds)) {
            $selectedId = $groupIds[$input - 1];
        } elseif (strpos($input, '-100') === 0) {
            $selectedId = $input;
        } else {
            echo "❌ Invalid input\n";
            return false;
        }
        
        // Ensure proper format
        if (strpos($selectedId, '-100') === 0) {
            $finalId = $selectedId;
        } else {
            $finalId = '-100' . abs($selectedId);
        }
        
        echo "\n✨ Setting up group ID: $finalId\n";
        
        return $this->updateEnvFile($finalId);
    }
    
    private function getUpdates() {
        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/getUpdates";
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                return $response->json('result');
            }
            
            echo "⚠️  API Error: " . $response->json('description') . "\n";
            return null;
        } catch (\Exception $e) {
            echo "❌ Connection Error: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    private function extractGroupIds($updates) {
        $groupIds = [];
        
        foreach ($updates as $update) {
            // Check for group/supergroup messages
            if (isset($update['message']['chat']['id'])) {
                $chatId = $update['message']['chat']['id'];
                $chatType = $update['message']['chat']['type'] ?? 'unknown';
                
                // Only collect group and supergroup IDs
                if (in_array($chatType, ['group', 'supergroup'])) {
                    if (!in_array($chatId, $groupIds)) {
                        $groupIds[] = $chatId;
                    }
                }
            }
            
            // Check for callback queries and other message types
            if (isset($update['callback_query']['message']['chat']['id'])) {
                $chatId = $update['callback_query']['message']['chat']['id'];
                $chatType = $update['callback_query']['message']['chat']['type'] ?? 'unknown';
                
                if (in_array($chatType, ['group', 'supergroup'])) {
                    if (!in_array($chatId, $groupIds)) {
                        $groupIds[] = $chatId;
                    }
                }
            }
        }
        
        return $groupIds;
    }
    
    private function updateEnvFile($groupId) {
        try {
            $envContent = file_get_contents($this->envPath);
            
            // Replace or add TELEGRAM_CHANNEL_ID
            if (strpos($envContent, 'TELEGRAM_CHANNEL_ID') !== false) {
                $envContent = preg_replace(
                    '/TELEGRAM_CHANNEL_ID=.*/',
                    'TELEGRAM_CHANNEL_ID=' . $groupId,
                    $envContent
                );
            } else {
                $envContent .= "\nTELEGRAM_CHANNEL_ID=" . $groupId;
            }
            
            file_put_contents($this->envPath, $envContent);
            
            echo "✅ .env file updated successfully\n";
            echo "📝 TELEGRAM_CHANNEL_ID=$groupId\n\n";
            
            // Clear config cache
            echo "🔄 Rebuilding Laravel config cache...\n";
            system('php artisan config:clear');
            system('php artisan config:cache');
            
            echo "✅ Configuration cache rebuilt\n\n";
            echo "🎉 Setup complete! Test the contact button now.\n";
            
            return true;
        } catch (\Exception $e) {
            echo "❌ Error updating .env: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function showManualMethod() {
        echo "\n📋 MANUAL METHOD:\n";
        echo "─────────────────────────────────────────────────────────────\n";
        echo "1. Telegram ဖွင့်ပါ\n";
        echo "2. Private Group ဖန်တီးပါ (Private ဆိုတာသေချာပါစေ)\n";
        echo "3. ခင်ဗျား၏ Bot ကို group သို့ ထည့်ပါ\n";
        echo "4. Group အတွင်းသို့ /start ပို့ပါ\n";
        echo "5. Bot တွင် /getid ခလုတ်နှိပ်ပါ\n";
        echo "6. Group ID ကို .env မှာ ထည့်သွင်းပါ:\n";
        echo "   TELEGRAM_CHANNEL_ID=-100xxxxxxxxxx\n";
        echo "7. Terminal တွင် အောက်ပါ command လုပ်ပါ:\n";
        echo "   php artisan config:clear && php artisan config:cache\n";
        echo "\n";
    }
}

$setup = new TelegramGroupSetup();
$setup->run();
?>
