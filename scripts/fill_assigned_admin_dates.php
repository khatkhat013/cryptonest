<?php
require __DIR__ . '/../vendor/autoload.php';

// This helper prints an artisan one-liner you can run to backfill assigned_admin_date
echo "Run this command from the project root to backfill users with assigned_admin_id but null assigned_admin_date:\n\n";
echo "php -r \"require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$kernel=\$app->make(Illuminate\\Contracts\\Console\\Kernel::class); \$kernel->bootstrap(); \Illuminate\Support\Facades\DB::table('users')->whereNotNull('assigned_admin_id')->whereNull('assigned_admin_date')->update(['assigned_admin_date' => now()]); echo 'Updated assigned_admin_date for relevant users.\n';\"\n\n";
echo "Alternatively run within tinker: php artisan tinker then run:\nDB::table('users')->whereNotNull('assigned_admin_id')->whereNull('assigned_admin_date')->update(['assigned_admin_date' => now()]);\n";

exit(0);
