<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run every minute to catch deposits updated directly in DB.
Schedule::command('deposit:update-balance')->everyMinute();

// Run database backup to Telegram every night at midnight (configurable in .env).
Schedule::command('backup:database-telegram')
    ->dailyAt((string) env('DB_BACKUP_SCHEDULE_AT', '00:00'))
    ->timezone((string) env('DB_BACKUP_TIMEZONE', 'Asia/Yangon'))
    ->withoutOverlapping()
    ->runInBackground();
