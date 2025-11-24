<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request with comprehensive security headers.
     * Implements OWASP Top 10 protection against:
     * - XSS (Cross-Site Scripting)
     * - Clickjacking
     * - MIME type sniffing
     * - SSL/TLS downgrade attacks
     * - Information disclosure
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ===== OWASP A03:2021 – Injection Protection =====
        // Content Security Policy - Prevents XSS and injection attacks
        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com unpkg.com; " .
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; " .
            "img-src 'self' data: https:; " .
            "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com cdn.jsdelivr.net; " .
            "connect-src 'self' api.telegram.org cdn.jsdelivr.net unpkg.com; " .
            "worker-src 'self' blob:; " .
            "frame-ancestors 'none'; " .
            "form-action 'self'; " .
            "base-uri 'self'; " .
            "object-src 'none'; " .
            "upgrade-insecure-requests"
        );

        // ===== OWASP A01:2021 – Broken Access Control =====
        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');

        // ===== OWASP A05:2021 – Broken Access Control =====
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ===== Prevent XSS attacks in older browsers =====
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // ===== OWASP A02:2021 – Cryptographic Failures =====
        // Enforce HTTPS and set HSTS (HTTP Strict Transport Security)
        if (config('app.env') === 'production') {
            // 1 year HSTS with subdomains
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // ===== Referrer Policy - Privacy & Security =====
        // Don't send referrer to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ===== Permissions Policy (formerly Feature Policy) =====
        // Disable unnecessary browser features
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), ' .
            'microphone=(), ' .
            'camera=(), ' .
            'payment=(), ' .
            'usb=(), ' .
            'magnetometer=(), ' .
            'gyroscope=(), ' .
            'accelerometer=()'
        );

        // ===== Remove server information disclosure =====
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');

        // ===== Prevent information disclosure in errors =====
        if (config('app.debug') === false && config('app.env') === 'production') {
            // No detailed error messages in production
            $response->headers->set('X-Error-Level', 'public');
        }

        // ===== Additional security headers =====
        // Prevent DNS prefetching information leakage
        $response->headers->set('X-DNS-Prefetch-Control', 'off');

        return $response;
    }
}
