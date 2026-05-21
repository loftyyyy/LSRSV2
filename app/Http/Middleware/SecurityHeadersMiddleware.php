<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Adds critical security headers to all responses to protect against:
     * - Clickjacking (X-Frame-Options)
     * - MIME type sniffing (X-Content-Type-Options)
     * - XSS attacks (X-XSS-Protection)
     * - Man-in-the-middle attacks (Strict-Transport-Security)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent the application from being embedded in a frame (clickjacking protection)
        // SAMEORIGIN: Only allow framing from same origin
        $response->header('X-Frame-Options', 'SAMEORIGIN');

        // Prevent browsers from MIME-type sniffing a response away from declared Content-Type
        // This prevents attacks where a script/HTML file is served as text/plain
        $response->header('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection in older browsers (legacy)
        // Modern browsers use CSP, but this helps older IE browsers
        $response->header('X-XSS-Protection', '1; mode=block');

        // Strict Transport Security - Force HTTPS for 1 year on production
        // includeSubDomains: Apply to all subdomains
        // preload: Allow inclusion in HSTS preload list
        if (app()->environment('production')) {
            $response->header(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Referrer Policy - Limit what referer info is sent to other sites
        // strict-origin-when-cross-origin: Only send origin, not full URL, to cross-site requests
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - Disable unused browser features
        // Prevents malicious scripts from accessing camera, microphone, etc.
        $response->header(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=()'
        );

        // Additional recommended headers
        // Prevent reflected XSS (though modern browsers use CSP)
        if (app()->environment('production')) {
            // Only allow resources from same origin and https://cdn.jsdelivr.net for TailwindCSS
            $response->header(
                'Content-Security-Policy',
                "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'"
            );
        }

        return $response;
    }
}
