<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetupTelegramGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:setup-group';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Setup Telegram private group for admin notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("\n╔════════════════════════════════════════════════════════════╗");
        $this->info("║     TELEGRAM PRIVATE GROUP SETUP FOR CRYPTONEST          ║");
        $this->info("╚════════════════════════════════════════════════════════════╝\n");

        $botToken = config('services.telegram.bot_token');

        if (!$botToken) {
            $this->error('❌ Error: TELEGRAM_BOT_TOKEN not found in config');
            return 1;
        }

        $this->info("✅ Bot Token loaded: " . substr($botToken, 0, 20) . "...\n");

        $this->info("📋 SETUP INSTRUCTIONS:\n");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("1. Telegram ဖွင့်ပါ → '+' → 'New Group' → အမည်ထည့်ပါ");
        $this->line("2. Private ကိုသေချာ ရွေးချယ်ပါ");
        $this->line("3. ခင်ဗျား၏ Bot (@CryptoNest_Bot) ကို Group သို့ ထည့်ပါ");
        $this->line("4. Bot အတွက် Admin ခွင့်ပြုပါ");
        $this->line("5. Group အတွင်းသို့ /start ပို့ပါ\n");

        $this->info("🔍 Fetching updates from Telegram bot...\n");

        $updates = $this->getUpdates($botToken);

        if (!$updates) {
            $this->showManualMethod();
            return 1;
        }

        $groupIds = $this->extractGroupIds($updates);

        if (empty($groupIds)) {
            $this->warn("⚠️  No group IDs found");
            $this->showManualMethod();
            return 1;
        }

        $this->info("✅ Found " . count($groupIds) . " group(s):\n");

        foreach ($groupIds as $index => $id) {
            $formattedId = '-100' . abs($id);
            $this->line("  " . ($index + 1) . ". $formattedId");
        }

        $choice = $this->choice(
            "\n📝 Select group number",
            array_map(function ($id, $index) {
                return ($index + 1) . ". -100" . abs($id);
            }, $groupIds, array_keys($groupIds))
        );

        preg_match('/(\d+)\./', $choice, $matches);
        $selectedIndex = (int)$matches[1] - 1;
        $selectedId = $groupIds[$selectedIndex];

        $finalId = strpos($selectedId, '-100') === 0 ? $selectedId : '-100' . abs($selectedId);

        $this->info("\n✨ Setting up group ID: $finalId\n");

        if ($this->updateEnvFile($finalId)) {
            $this->info("✅ TELEGRAM_CHANNEL_ID=$finalId\n");
            $this->info("🔄 Rebuilding Laravel config cache...");
            
            $this->call('config:clear');
            $this->call('config:cache');
            
            $this->info("✅ Configuration cache rebuilt\n");
            $this->info("🎉 Setup complete!");
            $this->info("📍 Next: Test the contact button on landing page\n");
            
            return 0;
        }

        return 1;
    }

    /**
     * Get updates from Telegram bot
     */
    private function getUpdates($botToken)
    {
        try {
            $url = "https://api.telegram.org/bot{$botToken}/getUpdates";
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json('result');
            }

            $this->error("⚠️  API Error: " . $response->json('description'));
            return null;
        } catch (\Exception $e) {
            $this->error("❌ Connection Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract group IDs from updates
     */
    private function extractGroupIds($updates)
    {
        $groupIds = [];

        foreach ($updates as $update) {
            // Check for group/supergroup messages
            if (isset($update['message']['chat']['id'])) {
                $chatId = $update['message']['chat']['id'];
                $chatType = $update['message']['chat']['type'] ?? 'unknown';

                if (in_array($chatType, ['group', 'supergroup'])) {
                    if (!in_array($chatId, $groupIds)) {
                        $groupIds[] = $chatId;
                    }
                }
            }

            // Check for callback queries
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

    /**
     * Update .env file with group ID
     */
    private function updateEnvFile($groupId)
    {
        try {
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            if (strpos($envContent, 'TELEGRAM_CHANNEL_ID') !== false) {
                $envContent = preg_replace(
                    '/TELEGRAM_CHANNEL_ID=.*/',
                    'TELEGRAM_CHANNEL_ID=' . $groupId,
                    $envContent
                );
            } else {
                $envContent .= "\nTELEGRAM_CHANNEL_ID=" . $groupId;
            }

            file_put_contents($envPath, $envContent);
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Error updating .env: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Show manual setup method
     */
    private function showManualMethod()
    {
        $this->info("\n📋 MANUAL METHOD:\n");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("1. Telegram ဖွင့်ပါ");
        $this->line("2. Private Group ဖန်တီးပါ");
        $this->line("3. Bot ကို group သို့ ထည့်ပါ");
        $this->line("4. Group အတွင်းသို့ /start ပို့ပါ");
        $this->line("5. @userinfobot ကို search ပြီး /start ခလုတ်နှိပ်ပါ");
        $this->line("6. Group ID ကို နုတ်ယူပါ (format: -100xxxxx)");
        $this->line("7. .env ဖိုင်တွင် ထည့်သွင်းပါ:");
        $this->line("   TELEGRAM_CHANNEL_ID=-100xxxxxxxxxx");
        $this->line("8. Terminal တွင် run ပါ:");
        $this->line("   php artisan config:clear && php artisan config:cache\n");
    }
}
