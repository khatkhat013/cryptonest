<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Deposit;

class SyncDepositStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposits:sync-statuses {--credit : Also attempt to credit wallets for deposits with complete status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync deposits.status to match action_statuses.name; optionally credit wallets for complete deposits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Deprecated: the legacy `deposits.status` column is being removed.
        // This command is retained for compatibility but no longer performs any sync.
        $this->info('deposits:sync-statuses is deprecated. The deposits.status column has been removed.');
        return 0;
    }
}
