<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Observers\DepositCreditingObserver;
use App\Observers\WithdrawalObserver;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for deposit/withdrawal status changes
        if (class_exists(Deposit::class) && class_exists(DepositCreditingObserver::class)) {
            Deposit::observe(DepositCreditingObserver::class);
        }
        if (class_exists(Withdrawal::class) && class_exists(WithdrawalObserver::class)) {
            Withdrawal::observe(WithdrawalObserver::class);
        }
        // Register observer to auto-create user wallets on registration
        if (class_exists(User::class) && class_exists(UserObserver::class)) {
            User::observe(UserObserver::class);
        }
    }
}
