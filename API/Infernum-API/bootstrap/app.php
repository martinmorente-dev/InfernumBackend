<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'api/v1/stripe/webhook'
        ]);

        $middleware->alias([
            'refresh.token' => App\Http\Middleware\RefreshTokenMiddleware::class,
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request)
        {
            if ($request->is('api/*') || $request->expectsJson())
            {
                return response()->json([
                    'status' => 'Failure',
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson())
            {
                return response()->json([
                    'status' => 'Failure',
                    'message' => 'User not authenticated',
                    'error' => 'Unauthorized',
                ], 401);
            }
        });
    })
    ->create();
