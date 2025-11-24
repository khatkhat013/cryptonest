<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Security middleware - OWASP Top 10 protection
        // Run SecurityHeaders first (prepend ensures it runs before other middleware)
        $middleware->prepend(\App\Http\Middleware\SecurityHeaders::class);

        // Global middleware for all requests
        $middleware->append(\App\Http\Middleware\RateLimitEndpoints::class);
        $middleware->append(\App\Http\Middleware\SetCacheHeaders::class);
        $middleware->append(\App\Http\Middleware\SanitizeInput::class);

        // Middleware aliases
        $middleware->alias([
            'admin-approval' => \App\Http\Middleware\AdminApprovalRequired::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
