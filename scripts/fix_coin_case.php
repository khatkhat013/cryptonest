<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Add credited_at column if not exists
if (!Schema::hasColumn('deposits', 'credited_at')) {
    Schema::table('deposits', function (Blueprint $table) {
        $table->timestamp('credited_at')->nullable()->after('action_status_id');
    });
    echo "Added credited_at column\n";
}

// Ensure all coins in user_wallets are uppercase
DB::table('user_wallets')
    ->whereRaw('coin <> UPPER(coin)')
    ->update(['coin' => DB::raw('UPPER(coin)')]);

echo "Updated wallet coins to uppercase\n";

// Ensure all coins in deposits are uppercase
DB::table('deposits')
    ->whereRaw('coin <> UPPER(coin)')
    ->update(['coin' => DB::raw('UPPER(coin)')]);

echo "Updated deposit coins to uppercase\n";