<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserWallet;
use App\Models\User;

class CheckWalletIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     * Use --fix to attempt automatic remap of wallets that reference non-existent users.
     *
     * @var string
     */
    protected $signature = 'wallets:check {--fix : Attempt to remap invalid user_id values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user_wallets for invalid user_id references and optionally fix them.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning user_wallets for invalid user_id references...');
        $invalid = [];
        $all = UserWallet::all();
        foreach ($all as $w) {
            $uid = $w->user_id;
            $user = User::find($uid);
            if (!$user) {
                $invalid[] = ['wallet_id' => $w->id, 'user_id' => $uid, 'coin' => $w->coin, 'balance' => $w->balance];
            }
        }

        if (empty($invalid)) {
            $this->info('No invalid wallet references found.');
            return 0;
        }

        $this->table(['wallet_id','user_id','coin','balance'], $invalid);

        if ($this->option('fix')) {
            $this->info('Attempting to remap by matching zero-padded display user_id...');
            $fixed = [];
            foreach ($invalid as $row) {
                $w = UserWallet::find($row['wallet_id']);
                if (!$w) continue;
                $current = $w->user_id;
                $padded = str_pad((string)$current, 6, '0', STR_PAD_LEFT);
                $userByUserId = User::where('user_id', $padded)->first();
                if ($userByUserId) {
                    $w->user_id = $userByUserId->id;
                    $w->save();
                    $fixed[] = ['wallet_id' => $w->id, 'from' => $current, 'to' => $userByUserId->id];
                }
            }
            if (empty($fixed)) {
                $this->warn('No wallets could be remapped automatically. Manual inspection required.');
            } else {
                $this->info('Remapped wallets:');
                $this->table(['wallet_id','from','to'], $fixed);
            }
        }

        return 0;
    }
}
