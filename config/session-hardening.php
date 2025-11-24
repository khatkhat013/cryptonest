<?php

/**
 * ===== OWASP A01:2021 – Broken Access Control =====
 * ===== OWASP A07:2021 – Identification & Authentication Failures =====
 * 
 * Session security hardening configuration:
 * - Secure cookie settings (HttpOnly, Secure, SameSite)
 * - Session timeout after inactivity
 * - Session regeneration on login
 * - Prevents session fixation attacks
 * - Prevents CSRF attacks via SameSite attribute
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    */
    'driver' => env('SESSION_DRIVER', 'cookie'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    | After this many minutes of inactivity, session expires
    | Recommended: 30 minutes for general users, 120 minutes for admins
    */
    'lifetime' => env('SESSION_LIFETIME', 30),

    'expire_on_close' => false,

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    | Encrypt session cookies in transit and at rest
    */
    'encrypt' => true,

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only
    |--------------------------------------------------------------------------
    | Session cookies cannot be accessed via JavaScript
    | Prevents XSS from stealing session tokens
    * 
    * ===== OWASP A03:2021 – Injection =====
    | ===== OWASP A07:2021 – Cross-Site Scripting (XSS) =====
    */
    'http_only' => true,

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only
    |--------------------------------------------------------------------------
    | Session cookies only sent over HTTPS
    | Prevents man-in-the-middle attacks
    * 
    * ===== OWASP A02:2021 – Cryptographic Failures =====
    */
    'secure' => env('SESSION_SECURE', true),

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    | Prevent CSRF attacks by restricting cross-site cookie sending
    * 
    * ===== OWASP A01:2021 – Cross-Site Request Forgery (CSRF) =====
    | 
    | Options:
    | - 'strict': Cookie only sent in same-site requests
    | - 'lax': Cookie sent in same-site + safe cross-site requests (GET)
    | - 'none': Cookie sent in all requests (must use Secure flag)
    | 
    | Recommended: 'lax' for general applications, 'strict' for sensitive operations
    */
    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    | Restricts cookie to specific path to reduce attack surface
    */
    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    | Restricts cookie to specific domain
    */
    'domain' => env('SESSION_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Regenerate Session ID on Login
    |--------------------------------------------------------------------------
    | Prevents session fixation attacks
    | Configure in app/Http/Controllers/Auth/ controllers
    */
    'regenerate_on_login' => true,

    /*
    |--------------------------------------------------------------------------
    | Regenerate Session ID on Logout
    |--------------------------------------------------------------------------
    | Prevents session fixation attacks during logout
    */
    'regenerate_on_logout' => true,

];
