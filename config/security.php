<?php

/**
 * ===== OWASP A07:2021 – Identification & Authentication Failures =====
 * ===== OWASP A04:2021 – Insecure Design =====
 * 
 * Security Configuration - Rate Limiting, Trusted IPs, and Security Settings
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    | Granular rate limits per endpoint type to prevent:
    | - Brute force attacks (login endpoints)
    | - API abuse (API endpoints)
    | - Resource exhaustion (payment endpoints)
    */

    'rate_limits' => [
        // Critical authentication endpoints: 5 requests per minute
        'critical' => [
            'max_attempts' => env('RATE_LIMIT_CRITICAL_MAX', 5),
            'decay_minutes' => env('RATE_LIMIT_CRITICAL_DECAY', 1),
            'endpoints' => [
                'admin/login',
                'admin/auth',
                'admin/forgot-password',
                'admin/reset-password',
                'api/auth/login',
                'api/admin/login',
                'login',
                'register',
            ],
        ],

        // API endpoints: 60 requests per minute
        'api' => [
            'max_attempts' => env('RATE_LIMIT_API_MAX', 60),
            'decay_minutes' => env('RATE_LIMIT_API_DECAY', 1),
        ],

        // Payment-related endpoints: 10 requests per minute
        'payment' => [
            'max_attempts' => env('RATE_LIMIT_PAYMENT_MAX', 10),
            'decay_minutes' => env('RATE_LIMIT_PAYMENT_DECAY', 1),
            'endpoints' => [
                'payment',
                'deposit',
                'withdraw',
                'transaction',
                'checkout',
            ],
        ],

        // General endpoints: 1000 requests per hour
        'general' => [
            'max_attempts' => env('RATE_LIMIT_GENERAL_MAX', 1000),
            'decay_minutes' => env('RATE_LIMIT_GENERAL_DECAY', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted IPs (Rate Limit Bypass)
    |--------------------------------------------------------------------------
    | IPs in this list bypass rate limiting (useful for internal monitoring)
    | Use comma-separated list in .env: SECURITY_TRUSTED_IPS=192.168.1.1,10.0.0.1
    */
    'trusted_ips' => array_filter(
        explode(',', env('SECURITY_TRUSTED_IPS', ''))
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Security Settings
    |--------------------------------------------------------------------------
    */
    'session' => [
        // Session timeout (minutes of inactivity)
        'lifetime' => env('SESSION_LIFETIME', 30),

        // Require HTTPS for session cookies
        'secure' => env('SESSION_SECURE_COOKIE', true),

        // HttpOnly flag (prevent JavaScript access)
        'http_only' => env('SESSION_HTTP_ONLY', true),

        // SameSite attribute (prevent CSRF)
        'same_site' => env('SESSION_SAME_SITE', 'strict'),

        // Encrypt session data
        'encrypt' => env('SESSION_ENCRYPT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy (CSP)
    |--------------------------------------------------------------------------
    | Prevent XSS attacks by restricting resource loading
    */
    'csp' => [
        // Default: only allow resources from same origin
        'default-src' => "'self'",

        // Allow scripts from self and CDNs
        'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com unpkg.com",

        // Allow styles from self and CDNs
        'style-src' => "'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com",

        // Allow images from any source (safe)
        'img-src' => "'self' data: https:",

        // Allow fonts from self and CDNs (bootstrap-icons, Font Awesome, etc)
        'font-src' => "'self' fonts.gstatic.com cdnjs.cloudflare.com cdn.jsdelivr.net",

        // Restrict form submission
        'form-action' => "'self'",

        // Restrict frame embedding
        'frame-ancestors' => "'none'",

        // Allow connections to self, Telegram API, and CDNs for WASM and source maps
        'connect-src' => "'self' api.telegram.org cdn.jsdelivr.net unpkg.com lottie.host",

        // Allow Web Workers and blob URLs for WASM
        'worker-src' => "'self' blob:",

        // Disallow plugin execution
        'object-src' => "'none'",

        // Use HTTPS for upgradeable requests
        'upgrade-insecure-requests' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    | HTTP headers to prevent common attacks
    */
    'headers' => [
        // Prevent clickjacking
        'X-Frame-Options' => 'DENY',

        // Prevent MIME type sniffing
        'X-Content-Type-Options' => 'nosniff',

        // Enable browser XSS filter
        'X-XSS-Protection' => '1; mode=block',

        // Enforce HTTPS (1 year) - production only
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',

        // Privacy: don't send referrer cross-site
        'Referrer-Policy' => 'strict-origin-when-cross-origin',

        // Disable unnecessary browser features
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=()',
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Sanitization
    |--------------------------------------------------------------------------
    | Prevent XSS and injection attacks
    */
    'sanitization' => [
        // HTML encode special characters
        'html_encode' => true,

        // Remove null bytes (\0)
        'remove_null_bytes' => true,

        // Strip HTML tags (defense in depth)
        'strip_tags' => true,

        // Routes to skip sanitization (API endpoints, JSON APIs)
        'skip_routes' => [
            'api/*',
            'api/**',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Security
    |--------------------------------------------------------------------------
    | Additional database security measures
    */
    'database' => [
        // Log all database queries (development/testing)
        'log_queries' => env('DB_LOG_QUERIES', false),

        // Log slow queries (over X milliseconds)
        'log_slow_queries' => env('DB_LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000),

        // Log sensitive operations (CREATE, UPDATE, DELETE)
        'log_audit_operations' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging & Monitoring
    |--------------------------------------------------------------------------
    | Security event logging configuration
    */
    'logging' => [
        // Log authentication attempts
        'log_auth_attempts' => true,

        // Log failed authentication
        'log_auth_failures' => true,

        // Log rate limit violations
        'log_rate_limit_violations' => true,

        // Log database operations (CREATE, UPDATE, DELETE)
        'log_database_operations' => true,

        // Log suspicious input patterns
        'log_suspicious_input' => true,

        // Default log level for security events
        'security_event_level' => env('SECURITY_LOG_LEVEL', 'warning'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    | Enforce strong passwords
    */
    'password' => [
        // Minimum password length
        'min_length' => env('PASSWORD_MIN_LENGTH', 8),

        // Require uppercase letters
        'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),

        // Require lowercase letters
        'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),

        // Require numbers
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),

        // Require special characters
        'require_special_chars' => env('PASSWORD_REQUIRE_SPECIAL_CHARS', false),

        // Prevent password reuse (last N passwords)
        'prevent_reuse' => env('PASSWORD_PREVENT_REUSE', 3),

        // Password expiration (days, 0 = no expiration)
        'expiration_days' => env('PASSWORD_EXPIRATION_DAYS', 0),

        // Password history length
        'history_length' => env('PASSWORD_HISTORY_LENGTH', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug & Error Handling
    |--------------------------------------------------------------------------
    | Security-aware error handling
    */
    'error_handling' => [
        // Show detailed errors (development only)
        'debug_mode' => env('APP_DEBUG', false),

        // Hide stack traces from users (production)
        'hide_stack_traces' => !env('APP_DEBUG', true),

        // Log all exceptions to file
        'log_exceptions' => true,

        // Send critical errors to security team
        'alert_on_critical' => env('SECURITY_ALERT_EMAIL', null),
    ],

];
