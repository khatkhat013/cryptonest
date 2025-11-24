<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * ===== OWASP A07:2021 – Identification & Authentication Failures =====
 * ===== OWASP A04:2021 – Insecure Design =====
 * 
 * Implements granular rate limiting per endpoint to prevent:
 * - Brute force attacks on authentication
 * - API abuse and DDoS attacks
 * - Resource exhaustion
 * 
 * Rate Limit Tiers:
 * - Login/Admin endpoints: 5 requests per minute
 * - User assignment API: 60 requests per minute
 * - General endpoints: 1000 requests per hour
 * - Payment/sensitive: 10 requests per minute
 */
class RateLimitEndpoints
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Bypass rate limiting for trusted admin IPs (if configured)
        if ($this->isTrustedIp($request)) {
            return $next($request);
        }

        // Define per-endpoint rate limits
        $routeName = $request->route()?->getName() ?? 'unknown';
        $method = $request->getMethod();
        $path = $request->path();
        $clientId = $this->getClientIdentifier($request);

        // Critical endpoints: 5 requests per minute
        if ($this->isCriticalEndpoint($path)) {
            $key = "critical:{$clientId}:{$path}";
            
            if ($this->limiter->tooManyAttempts($key, 5)) {
                \Illuminate\Support\Facades\Log::warning('Rate limit exceeded - critical endpoint', [
                    'path' => $path,
                    'client' => $clientId,
                    'method' => $method,
                ]);

                return $this->tooManyAttemptsResponse($request, 'Too many attempts. Please try again in 1 minute.');
            }

            $this->limiter->hit($key, 60); // 1 minute
        }

        // API endpoints: 60 requests per minute
        elseif ($this->isApiEndpoint($path)) {
            $key = "api:{$clientId}:{$path}";
            
            if ($this->limiter->tooManyAttempts($key, 60)) {
                \Illuminate\Support\Facades\Log::warning('Rate limit exceeded - API endpoint', [
                    'path' => $path,
                    'client' => $clientId,
                ]);

                return $this->tooManyAttemptsResponse($request, 'API rate limit exceeded. Maximum 60 requests per minute.');
            }

            $this->limiter->hit($key, 60);
        }

        // Payment endpoints: 10 requests per minute
        elseif ($this->isPaymentEndpoint($path)) {
            $key = "payment:{$clientId}:{$path}";
            
            if ($this->limiter->tooManyAttempts($key, 10)) {
                \Illuminate\Support\Facades\Log::warning('Rate limit exceeded - payment endpoint', [
                    'path' => $path,
                    'client' => $clientId,
                ]);

                return $this->tooManyAttemptsResponse($request, 'Payment operation rate limited. Please try again later.');
            }

            $this->limiter->hit($key, 60);
        }

        // General endpoints: 1000 requests per hour
        else {
            $key = "general:{$clientId}";
            
            if ($this->limiter->tooManyAttempts($key, 1000)) {
                \Illuminate\Support\Facades\Log::warning('Rate limit exceeded - general endpoint', [
                    'path' => $path,
                    'client' => $clientId,
                ]);

                return $this->tooManyAttemptsResponse($request, 'Too many requests. Please try again later.');
            }

            $this->limiter->hit($key, 3600); // 1 hour
        }

        return $next($request);
    }

    /**
     * Check if endpoint is critical (auth/admin operations)
     */
    protected function isCriticalEndpoint(string $path): bool
    {
        $criticalPaths = [
            'admin/login',
            'admin/auth',
            'admin/forgot-password',
            'admin/reset-password',
            'api/auth/login',
            'api/admin/login',
            'login',
            'register',
        ];

        foreach ($criticalPaths as $criticalPath) {
            if (str_contains($path, $criticalPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if endpoint is API endpoint
     */
    protected function isApiEndpoint(string $path): bool
    {
        return str_starts_with($path, 'api/') || str_contains($path, '/api/');
    }

    /**
     * Check if endpoint is payment-related
     */
    protected function isPaymentEndpoint(string $path): bool
    {
        $paymentPaths = [
            'payment',
            'deposit',
            'withdraw',
            'transaction',
            'checkout',
        ];

        foreach ($paymentPaths as $paymentPath) {
            if (str_contains($path, $paymentPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get unique client identifier (IP + user agent hash)
     * 
     * ===== OWASP A01:2021 – Logging & Monitoring =====
     */
    protected function getClientIdentifier(Request $request): string
    {
        if (auth()->check()) {
            return 'user_' . auth()->id();
        }

        // Use IP + user agent for anonymous users
        $ip = $request->ip();
        $userAgent = hash('sha256', $request->userAgent());
        
        return hash('sha256', "{$ip}:{$userAgent}");
    }

    /**
     * Check if request comes from trusted IP (admin network)
     */
    protected function isTrustedIp(Request $request): bool
    {
        $trustedIps = config('security.trusted_ips', []);
        
        if (empty($trustedIps)) {
            return false;
        }

        return in_array($request->ip(), $trustedIps);
    }

    /**
     * Return rate limit exceeded response
     */
    protected function tooManyAttemptsResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(
                ['error' => $message],
                429 // Too Many Requests
            );
        }

        return response()->view('errors.429', ['message' => $message], 429);
    }
}
