<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        $exceptions->render(function (AuthenticationException $exception, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'unauthenticated',
                    'message' => $exception->getMessage() ?: 'Authentication is required to access this resource.'
                ], 401);
            }

            return response()->json([
                'error' => 'unauthenticated',
                'message' => 'Authentication is required to access this resource.'
            ], 401);
        });

        $exceptions->render(function (AuthorizationException $exception, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'forbidden',
                    'message' => $exception->getMessage() ?: 'You do not have permission to perform this action.'
                ], 403);
            }

            return response()->json([
                'error' => 'forbidden',
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        });

        $exceptions->render(function (ModelNotFoundException $exception, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'not_found',
                    'message' => 'The requested resource could not be found.'
                ], 404);
            }

            return response()->json([
                'error' => 'not_found',
                'message' => 'The requested resource could not be found.'
            ], 404);
        });

        $exceptions->render(function (ValidationException $exception, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'validation_error',
                    'message' => 'The given data was invalid.',
                    'details' => $exception->errors(),
                ], 422);
            }

            return response()->json([
                'error' => 'validation_error',
                'message' => 'The given data was invalid.',
                'details' => $exception->errors(),
            ], 422);
        });

        $exceptions->render(function (HttpExceptionInterface $exception, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'http_error',
                    'message' => $exception->getMessage() ?: 'An unexpected error occurred.'
                ], $exception->getStatusCode());
            }

            return response()->json([
                'error' => 'http_error',
                'message' => $exception->getMessage() ?: 'An unexpected error occurred.'
            ], $exception->getStatusCode());
        });
    })->create();