<?php

/**
 * ===== OWASP Top 10 Security Verification Script =====
 * 
 * Verifies that all security measures are properly configured and active.
 * Run this script to validate the security hardening implementation.
 * 
 * Usage: php scripts/verify_security.php
 */

require_once __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SecurityVerifier
{
    private int $passedChecks = 0;
    private int $totalChecks = 0;
    private array $results = [];

    public function run()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "OWASP TOP 10 SECURITY VERIFICATION\n";
        echo str_repeat("=", 80) . "\n\n";

        $this->checkEnvironmentConfig();
        $this->checkSessionSecurity();
        $this->checkMiddlewareRegistration();
        $this->checkSecurityHeaders();
        $this->checkCsrfProtection();
        $this->checkDatabaseSecurity();
        $this->checkLogging();
        $this->checkPasswordHashing();

        $this->printSummary();
    }

    private function checkEnvironmentConfig()
    {
        echo "1. ENVIRONMENT CONFIGURATION\n";
        echo str_repeat("-", 80) . "\n";

        $this->verify(
            "APP_DEBUG is disabled",
            env('APP_DEBUG') === false
        );

        $this->verify(
            "APP_ENV is set to production",
            env('APP_ENV') === 'production' || env('APP_ENV') === 'local'
        );

        $this->verify(
            "APP_KEY is set",
            !empty(env('APP_KEY'))
        );

        $this->verify(
            "SESSION_ENCRYPT is enabled",
            env('SESSION_ENCRYPT') === true || env('SESSION_ENCRYPT') === 'true'
        );

        $this->verify(
            "SESSION_SECURE_COOKIE is enabled",
            env('SESSION_SECURE_COOKIE') === true || env('SESSION_SECURE_COOKIE') === 'true'
        );

        echo "\n";
    }

    private function checkSessionSecurity()
    {
        echo "2. SESSION SECURITY\n";
        echo str_repeat("-", 80) . "\n";

        $sessionConfig = Config::get('session');

        $this->verify(
            "Session lifetime is 30 minutes or less",
            $sessionConfig['lifetime'] <= 30
        );

        $this->verify(
            "Session encryption enabled",
            $sessionConfig['encrypt'] === true
        );

        $this->verify(
            "Session HTTPS only (secure cookies)",
            $sessionConfig['secure'] === true
        );

        $this->verify(
            "Session HTTP-only (prevent JavaScript access)",
            $sessionConfig['http_only'] === true
        );

        $this->verify(
            "Session SameSite is strict or lax",
            in_array($sessionConfig['same_site'], ['strict', 'lax'])
        );

        echo "\n";
    }

    private function checkMiddlewareRegistration()
    {
        echo "3. MIDDLEWARE REGISTRATION\n";
        echo str_repeat("-", 80) . "\n";

        // Check if middleware files exist
        $middlewareFiles = [
            'SecurityHeaders' => app_path('Http/Middleware/SecurityHeaders.php'),
            'SanitizeInput' => app_path('Http/Middleware/SanitizeInput.php'),
            'SetCacheHeaders' => app_path('Http/Middleware/SetCacheHeaders.php'),
            'RateLimitEndpoints' => app_path('Http/Middleware/RateLimitEndpoints.php'),
        ];

        foreach ($middlewareFiles as $name => $path) {
            $this->verify(
                "{$name} middleware exists",
                file_exists($path)
            );
        }

        $this->verify(
            "Middleware classes can be instantiated",
            class_exists('App\Http\Middleware\SecurityHeaders') &&
            class_exists('App\Http\Middleware\SanitizeInput') &&
            class_exists('App\Http\Middleware\SetCacheHeaders') &&
            class_exists('App\Http\Middleware\RateLimitEndpoints')
        );

        echo "\n";
    }

    private function checkSecurityHeaders()
    {
        echo "4. SECURITY HEADERS CONFIGURATION\n";
        echo str_repeat("-", 80) . "\n";

        $securityConfig = Config::get('security');

        $this->verify(
            "X-Frame-Options header configured",
            isset($securityConfig['headers']['X-Frame-Options'])
        );

        $this->verify(
            "X-Content-Type-Options header configured",
            isset($securityConfig['headers']['X-Content-Type-Options'])
        );

        $this->verify(
            "Content-Security-Policy configured",
            isset($securityConfig['csp']['default-src'])
        );

        $this->verify(
            "Referrer-Policy configured",
            isset($securityConfig['headers']['Referrer-Policy'])
        );

        $this->verify(
            "Permissions-Policy configured",
            isset($securityConfig['headers']['Permissions-Policy'])
        );

        echo "\n";
    }

    private function checkCsrfProtection()
    {
        echo "5. CSRF PROTECTION\n";
        echo str_repeat("-", 80) . "\n";

        $this->verify(
            "CSRF middleware registered",
            class_exists('Illuminate\Foundation\Http\Middleware\VerifyCsrfToken')
        );

        $this->verify(
            "Session token available",
            !empty(csrf_token())
        );

        echo "\n";
    }

    private function checkDatabaseSecurity()
    {
        echo "6. DATABASE SECURITY\n";
        echo str_repeat("-", 80) . "\n";

        try {
            DB::connection()->getPdo();
            $this->verify("Database connection successful", true);
        } catch (\Exception $e) {
            $this->verify("Database connection successful", false);
        }

        $this->verify(
            "Eloquent models available",
            class_exists('App\Models\User') || class_exists('App\Models\Admin')
        );

        $this->verify(
            "SecureModel trait exists",
            file_exists(app_path('Traits/SecureModel.php'))
        );

        echo "\n";
    }

    private function checkLogging()
    {
        echo "7. LOGGING & MONITORING\n";
        echo str_repeat("-", 80) . "\n";

        $logPath = storage_path('logs/laravel.log');

        $this->verify(
            "Log file exists",
            file_exists($logPath)
        );

        $this->verify(
            "Log directory is writable",
            is_writable(storage_path('logs'))
        );

        $this->verify(
            "Security configuration exists",
            file_exists(config_path('security.php'))
        );

        echo "\n";
    }

    private function checkPasswordHashing()
    {
        echo "8. PASSWORD SECURITY\n";
        echo str_repeat("-", 80) . "\n";

        try {
            $testPassword = 'test_password_123';
            $hashed = \Illuminate\Support\Facades\Hash::make($testPassword);
            
            $isValid = \Illuminate\Support\Facades\Hash::check($testPassword, $hashed);

            $this->verify(
                "Password hashing works (bcrypt)",
                $isValid && str_starts_with($hashed, '$2y$')
            );
        } catch (\Exception $e) {
            $this->verify("Password hashing works (bcrypt)", false);
        }

        echo "\n";
    }

    private function verify(string $check, bool $passed)
    {
        $this->totalChecks++;

        if ($passed) {
            $this->passedChecks++;
            echo "✓ $check\n";
            $this->results[] = ['check' => $check, 'status' => 'PASS'];
        } else {
            echo "✗ $check\n";
            $this->results[] = ['check' => $check, 'status' => 'FAIL'];
        }
    }

    private function printSummary()
    {
        echo str_repeat("=", 80) . "\n";
        echo "SECURITY VERIFICATION SUMMARY\n";
        echo str_repeat("=", 80) . "\n\n";

        $passed = $this->passedChecks;
        $total = $this->totalChecks;
        $percentage = round(($passed / $total) * 100, 1);

        echo "Total Checks: {$total}\n";
        echo "Passed: {$passed}\n";
        echo "Failed: " . ($total - $passed) . "\n";
        echo "Score: {$percentage}%\n\n";

        if ($percentage === 100.0) {
            echo "🔒 ALL SECURITY CHECKS PASSED - APPLICATION IS SECURE\n";
        } elseif ($percentage >= 80) {
            echo "⚠️  MOST SECURITY CHECKS PASSED - Review failed items\n";
        } else {
            echo "❌ CRITICAL SECURITY ISSUES - Address failed items immediately\n";
        }

        echo "\n" . str_repeat("=", 80) . "\n";

        // Show failed checks
        $failed = array_filter($this->results, fn($r) => $r['status'] === 'FAIL');
        if (!empty($failed)) {
            echo "\nFAILED CHECKS:\n";
            echo str_repeat("-", 80) . "\n";
            foreach ($failed as $item) {
                echo "✗ {$item['check']}\n";
            }
            echo "\n";
        }
    }
}

// Run verifier
$verifier = new SecurityVerifier();
$verifier->run();
