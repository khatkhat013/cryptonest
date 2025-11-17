<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

// This script updates existing users that have numeric user_id values less than START
// so that UID 000001 -> 342016, 000002 -> 342017, etc.

$START = 342016;
$OFFSET = $START - 1; // add this to existing numeric user_id values

$users = User::whereNotNull('user_id')->get();

if ($users->isEmpty()) {
    echo "No users with user_id found. Nothing to do.\n";
    exit(0);
}

foreach ($users as $user) {
    $cur = intval($user->user_id);
    if ($cur > 0 && $cur < $START) {
        $new = $cur + $OFFSET;
        $user->user_id = str_pad($new, 6, '0', STR_PAD_LEFT);
        $user->save();
        echo "Updated user id={$user->id} from {$cur} to {$user->user_id}\n";
    }
}

echo "Done. Please verify in the database that user_id values are updated as expected.\n";
