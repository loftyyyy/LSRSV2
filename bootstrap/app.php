<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
   ->withRouting(
       web: __DIR__.'/../routes/web.php',
       api: __DIR__.'/../routes/api.php',
       commands: __DIR__.'/../routes/console.php',
       health: '/up',
   )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases for easy use in routes
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Apply global middleware to all requests
        $middleware->append([
            // Security headers middleware - adds X-Frame-Options, X-Content-Type-Options, etc.
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            
            // Input sanitization middleware - strips XSS attempts from POST/PUT/PATCH requests
            \App\Http\Middleware\SanitizeInputMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
