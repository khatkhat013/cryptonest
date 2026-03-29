<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $shouldNoStore = false;

        // Static assets: Cache for 1 year (31536000 seconds)
        // Images, CSS, JS with versioning/hashing don't need frequent updates
        if ($this->isStaticAsset($request->path())) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
        // Redirects should never be cached for authenticated/admin flows.
        elseif ($response->isRedirection()) {
            $shouldNoStore = true;
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Vary', 'Cookie, Authorization');
        }
        // Admin/auth pages and any response that mutates cookies: never cache
        // This prevents stale CSRF/session pages that can trigger 419 errors.
        elseif (
            $request->is('admin/*') ||
            $request->is('login') ||
            $request->is('register') ||
            $response->headers->has('Set-Cookie')
        ) {
            $shouldNoStore = true;
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Vary', 'Cookie, Authorization');
        }
        // API responses: No caching
        elseif ($request->is('api/*')) {
            $shouldNoStore = true;
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Vary', 'Cookie, Authorization');
        }
        // HTML pages: no-store to avoid stale CSRF tokens in proxied environments
        elseif ($response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $shouldNoStore = true;
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Vary', 'Cookie, Authorization');
        }
        // Default: Cache for 1 hour (3600 seconds)
        else {
            $response->headers->set('Cache-Control', 'public, max-age=3600, must-revalidate');
        }

        // Add ETag for cache validation only for cacheable responses.
        if (!$shouldNoStore && $response->getContent() && !$response->headers->has('ETag')) {
            $response->setEtag(md5($response->getContent()));
        }

        // Add Last-Modified header only for cacheable responses.
        if (!$shouldNoStore && !$response->headers->has('Last-Modified')) {
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        }

        // For no-store responses, remove validators that encourage conditional caching.
        if ($shouldNoStore) {
            $response->headers->remove('ETag');
            $response->headers->remove('Last-Modified');
        }

        return $response;
    }

    /**
     * Check if the request is for a static asset
     *
     * @param  string  $path
     * @return bool
     */
    private function isStaticAsset(string $path): bool
    {
        // Prevent directory traversal attacks (../../../etc/passwd)
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            return false;
        }

        // Normalize path to lowercase for comparison
        $path = strtolower(trim($path));

        // Common static asset extensions
        $extensions = [
            // Images
            'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'ico', 'avif',
            // Styles
            'css',
            // Scripts
            'js', 'mjs',
            // Fonts
            'woff', 'woff2', 'ttf', 'eot', 'otf',
            // Media
            'mp4', 'webm', 'mp3', 'wav', 'ogg',
            // Documents
            'pdf', 'txt', 'xml', 'json',
        ];

        foreach ($extensions as $ext) {
            if (str_ends_with($path, '.' . $ext)) {
                return true;
            }
        }

        return false;
    }
}
