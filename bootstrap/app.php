<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'check.banned' => \App\Http\Middleware\CheckUserBanned::class,
            'portfolio.maintenance' => \App\Http\Middleware\PortfolioMaintenanceMode::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\PortfolioMaintenanceMode::class,
            \App\Http\Middleware\CheckUserBanned::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API error format
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.validation_failed'),
                    'errors' => $e->errors(),
                    'code' => 422,
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.unauthenticated'),
                    'code' => 401,
                ], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.forbidden'),
                    'code' => 403,
                ], 403);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.resource_not_found'),
                    'code' => 404,
                ], 404);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, Request $request) {
            if ($request->expectsJson()) {
                $status = $e->getStatusCode();
                $message = match ($status) {
                    401 => __('messages.unauthenticated'),
                    403 => __('messages.forbidden'),
                    404 => __('messages.resource_not_found'),
                    default => $e->getMessage() ?: __('messages.error_occurred'),
                };

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'code' => $status,
                ], $status);
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                \Illuminate\Support\Facades\Log::error('Unhandled API exception', [
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'path' => $request->path(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => __('messages.server_error'),
                    'code' => 500,
                ], 500);
            }
        });
    })->create();
