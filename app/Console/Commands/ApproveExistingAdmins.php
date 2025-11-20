<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class ApproveExistingAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admins:approve-existing {--super-admin-only : Only approve super admins}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve all existing unapproved admins (they existed before the approval system)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Admin::where('is_approved', false)
            ->whereNull('rejection_reason');

        if ($this->option('super-admin-only')) {
            $query->where('role_id', 2); // Assuming 2 is super admin role
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('No unapproved admins found.');
            return Command::SUCCESS;
        }

        if ($this->confirm("Approve {$count} existing admin(s)?", true)) {
            $query->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => 1, // System approval
            ]);

            $this->info("✓ Successfully approved {$count} admin(s)");
        } else {
            $this->info('Cancelled.');
        }

        return Command::SUCCESS;
    }
}
