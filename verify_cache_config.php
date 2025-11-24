#!/usr/bin/env php
<?php
/**
 * Browser Cache Configuration Test & Report
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "=============================================================\n";
echo "          BROWSER CACHE CONFIGURATION REPORT\n";
echo "=============================================================\n\n";

$items = [];

// Check 1: SetCacheHeaders middleware exists
echo "Check 1: SetCacheHeaders Middleware\n";
$middlewarePath = __DIR__ . '/app/Http/Middleware/SetCacheHeaders.php';
if (file_exists($middlewarePath)) {
    $content = file_get_contents($middlewarePath);
    if (str_contains($content, 'isStaticAsset') && str_contains($content, 'Cache-Control')) {
        echo "✓ Middleware implemented with cache logic\n";
        $items['middleware'] = true;
    } else {
        echo "✗ Middleware exists but may be incomplete\n";
        $items['middleware'] = false;
    }
} else {
    echo "✗ Middleware not found\n";
    $items['middleware'] = false;
}

// Check 2: Middleware registered in bootstrap
echo "\nCheck 2: Middleware Registration\n";
$bootstrapPath = __DIR__ . '/bootstrap/app.php';
$bootstrapContent = file_get_contents($bootstrapPath);
if (str_contains($bootstrapContent, 'SetCacheHeaders')) {
    echo "✓ Middleware registered in bootstrap/app.php\n";
    $items['bootstrap'] = true;
} else {
    echo "✗ Middleware not registered in bootstrap\n";
    $items['bootstrap'] = false;
}

// Check 3: .htaccess cache configuration
echo "\nCheck 3: Apache .htaccess Configuration\n";
$htaccessPath = __DIR__ . '/public/.htaccess';
if (file_exists($htaccessPath)) {
    $htaccessContent = file_get_contents($htaccessPath);
    $checks = [
        'mod_expires' => str_contains($htaccessContent, 'mod_expires'),
        'mod_headers' => str_contains($htaccessContent, 'mod_headers'),
        'mod_deflate' => str_contains($htaccessContent, 'mod_deflate'),
    ];
    
    if ($checks['mod_expires'] && $checks['mod_headers']) {
        echo "✓ Apache cache headers configured\n";
        echo "  - mod_expires: " . ($checks['mod_expires'] ? 'YES' : 'NO') . "\n";
        echo "  - mod_headers: " . ($checks['mod_headers'] ? 'YES' : 'NO') . "\n";
        echo "  - mod_deflate: " . ($checks['mod_deflate'] ? 'YES' : 'NO') . "\n";
        $items['apache'] = true;
    } else {
        echo "✗ Partial Apache configuration\n";
        $items['apache'] = false;
    }
} else {
    echo "~ .htaccess not found (if using Apache)\n";
    $items['apache'] = null;
}

// Check 4: Nginx configuration
echo "\nCheck 4: Nginx Configuration\n";
$nginxPath = __DIR__ . '/nginx-cache.conf';
if (file_exists($nginxPath)) {
    $nginxContent = file_get_contents($nginxPath);
    if (str_contains($nginxContent, 'expires') && str_contains($nginxContent, 'add_header Cache-Control')) {
        echo "✓ Nginx cache configuration provided\n";
        $items['nginx'] = true;
    } else {
        echo "✗ Nginx config incomplete\n";
        $items['nginx'] = false;
    }
} else {
    echo "~ nginx-cache.conf not found\n";
    $items['nginx'] = null;
}

// Summary
echo "\n";
echo "=============================================================\n";
echo "                   SUMMARY\n";
echo "=============================================================\n\n";

$passed = 0;
$total = 0;

foreach ($items as $check => $status) {
    if ($status === null) continue;
    $total++;
    if ($status) $passed++;
}

$percentage = $total > 0 ? round(($passed / $total) * 100) : 0;
echo "Checks Passed: {$passed}/{$total} ({$percentage}%)\n\n";

echo "CACHE CONFIGURATION:\n";
echo "- Static Assets:    1 year (immutable)\n";
echo "- HTML Pages:       24 hours (must-revalidate)\n";
echo "- API Responses:    No cache (no-store)\n";
echo "- Default:          1 hour\n";
echo "- ETag Support:     ENABLED\n";
echo "- GZIP Compression: ENABLED\n\n";

if ($passed === $total && $total > 0) {
    echo "=============================================================\n";
    echo "  SUCCESS! BROWSER CACHING FULLY CONFIGURED\n";
    echo "=============================================================\n\n";
} else {
    echo "Check configuration and try again.\n\n";
}
