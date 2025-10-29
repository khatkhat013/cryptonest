<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DepositAdminController;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;

$depositId = $argv[1] ?? 10;
$adminId = $argv[2] ?? 2;

$deposit = Deposit::find($depositId);
if (!$deposit) { echo "Deposit not found\n"; exit(1); }

// Login admin for guard
$admin = App\Models\Admin::find($adminId);
if (!$admin) { echo "Admin not found\n"; exit(1); }
Auth::guard('admin')->login($admin);

$request = Request::create('/admin/deposits/'.$deposit->id.'/status', 'POST', [
    'action_status_id' => \App\Models\ActionStatus::where('name','complete')->value('id'),
    'amount' => $deposit->amount
]);

$controller = new DepositAdminController();

$response = $controller->updateStatus($request, $deposit);

if ($response instanceof Illuminate\Http\JsonResponse) {
    echo "JSON Response: "; print_r($response->getData());
} elseif ($response instanceof Illuminate\Http\RedirectResponse) {
    echo "Redirected: "; print_r($response->getTargetUrl());
} else {
    echo "Response: "; var_dump($response);
}

echo "Done\n";
