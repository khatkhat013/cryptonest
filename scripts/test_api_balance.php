<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('user_id', '000001')->first();
$req = new Illuminate\Http\Request();
$req->setUserResolver(function() use ($user) { return $user; });
$ctrl = new App\Http\Controllers\Api\WalletApiController();
$resp = $ctrl->balance($req, 'btc');

echo $resp->getContent() . PHP_EOL;
