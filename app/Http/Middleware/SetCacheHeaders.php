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

        // Static assets: Cache for 1 year (31536000 seconds)
        // Images, CSS, JS with versioning/hashing don't need frequent updates
        if ($this->isStaticAsset($request->path())) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
        // API responses: No caching
        elseif ($request->is('api/*')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
        // HTML pages: Cache for short period with revalidation (24 hours)
        elseif ($response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $response->headers->set('Cache-Control', 'public, max-age=86400, must-revalidate');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        }
        // Default: Cache for 1 hour (3600 seconds)
        else {
            $response->headers->set('Cache-Control', 'public, max-age=3600, must-revalidate');
        }

        // Add ETag for cache validation
        if ($response->getContent() && !$response->headers->has('ETag')) {
            $response->setEtag(md5($response->getContent()));
        }

        // Add Last-Modified header
        if (!$response->headers->has('Last-Modified')) {
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
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
