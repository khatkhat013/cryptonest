<?php
/**
 * Test Landing Page Plan Contact Endpoint
 * Usage: php artisan tinker < test_endpoint.php
 */

// Simulate a POST request to /api/contact-admin
$admin = \App\Models\Admin::first();

if (!$admin) {
    echo "ERROR: No admin found in database\n";
    exit;
}

echo "Testing with Admin: " . $admin->name . " (ID: " . $admin->id . ")\n";
echo "Chat ID configured: " . env('TELEGRAM_CHANNEL_ID') . "\n\n";

// Simulate request
$request = new \Illuminate\Http\Request();
$request->merge(['plan_id' => 'standard']);
$request->setUserResolver(function () use ($admin) {
    return $admin;
});

// Force admin guard
Auth::guard('admin')->setUser($admin);

// Create controller and call method
$controller = new \App\Http\Controllers\PlanContactController();
$response = $controller->contactAdmin($request);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Body:\n";
echo json_encode($response->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
?>
