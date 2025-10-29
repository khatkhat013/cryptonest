<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\CheckWalletIntegrity;

class WalletIntegrityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register command when running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckWalletIntegrity::class,
            ]);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
