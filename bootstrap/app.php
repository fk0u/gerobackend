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
        // NOTE: Do NOT use EnsureFrontendRequestsAreStateful for pure API token auth (mobile apps)
        // That middleware is only for SPA cookie-based auth, not Bearer token auth
        
        // Register CORS middleware for API routes
        $middleware->appendToGroup('api', [\App\Http\Middleware\Cors::class]);
        
        // Register role authorization middleware alias
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleAuthorization::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();