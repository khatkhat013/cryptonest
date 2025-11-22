<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Admin;

$admin = Admin::where('name', 'Black Coder')->first();

if ($admin) {
    echo "Found admin: {$admin->name}\n";
    echo "Current role_id: {$admin->role_id}\n";
    $admin->update(['role_id' => 3]);
    echo "✅ Updated role_id to: 3 (Super Admin)\n";
    echo "Admin updated successfully!\n";
} else {
    echo "❌ Admin 'Black Coder' not found\n";
}
