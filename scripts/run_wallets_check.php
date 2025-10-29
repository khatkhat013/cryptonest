<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cmd = new App\Console\Commands\CheckWalletIntegrity();
$cmd->setLaravel(app());
$cmd->handle();
