<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SanitizeInputMiddleware
 *
 * Sanitizes all POST/PUT/PATCH request inputs by:
 * - Stripping dangerous HTML/JavaScript tags
 * - Converting special characters to HTML entities
 * - Preventing XSS attacks through form inputs
 *
 * Security Note: This middleware prevents common XSS vectors but is not a replacement
 * for proper output escaping in Blade templates. Always use {{ }} instead of {!! !!}
 * for user-supplied content unless you specifically need raw HTML.
 */
class SanitizeInputMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Sanitizes input data for POST, PUT, and PATCH requests while
     * preserving data integrity for legitimate use cases.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only sanitize POST, PUT, and PATCH requests that contain data
        if (($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) && $request->all()) {
            $this->sanitizeInput($request);
        }

        return $next($request);
    }

    /**
     * Recursively sanitize all input data
     */
    private function sanitizeInput(Request $request): void
    {
        // Fields that should NOT be sanitized (passwords, tokens, etc.)
        $skipFields = [
            'password',
            'password_confirmation',
            'confirm_password',
            'api_token',
            'access_token',
            'refresh_token',
            'secret',
            'private_key',
            'public_key',
            'otp',
            '_token', // Laravel CSRF token
        ];

        $sanitized = [];

        foreach ($request->all() as $key => $value) {
            // Skip sensitive fields - don't modify passwords or tokens
            if (in_array(strtolower($key), array_map('strtolower', $skipFields))) {
                $sanitized[$key] = $value;
            } elseif (is_string($value)) {
                // Sanitize string values
                $sanitized[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                // Recursively sanitize nested arrays
                $sanitized[$key] = $this->sanitizeArray($value, $skipFields);
            } else {
                // Keep non-string, non-array values unchanged (numbers, booleans, etc.)
                $sanitized[$key] = $value;
            }
        }

        // Replace request data with sanitized version
        $request->replace($sanitized);
    }

    /**
     * Sanitize a single string value
     * 
     * Removes dangerous tags while preserving safe formatting tags
     */
    private function sanitizeString(string $value): string
    {
        // Allow only safe HTML tags (formatting, links, lists)
        // Strip all other tags including script, iframe, embed, etc.
        $allowedTags = '<p><br><strong><b><em><i><u><a><ul><li><ol><h1><h2><h3><h4><h5><h6><blockquote>';
        $stripped = strip_tags($value, $allowedTags);

        // Convert special characters to HTML entities to prevent XSS
        // ENT_QUOTES: Encode both double and single quotes
        // ENT_SUBSTITUTE: Replace invalid characters with replacement character
        $sanitized = htmlspecialchars($stripped, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return $sanitized;
    }

    /**
     * Recursively sanitize array values
     */
    private function sanitizeArray(array $array, array $skipFields): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            // Skip sensitive fields
            if (in_array(strtolower($key), array_map('strtolower', $skipFields))) {
                $result[$key] = $value;
            } elseif (is_string($value)) {
                $result[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                $result[$key] = $this->sanitizeArray($value, $skipFields);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
