<?php

use App\Http\Middleware\SetStorefrontPreferences;
use App\Services\ErrorTrackingService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetStorefrontPreferences::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment/sslcommerz/*',
            'payment/stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $exception): void {
            app(ErrorTrackingService::class)->report($exception, [
                'url' => request()?->fullUrl(),
                'method' => request()?->method(),
                'ip' => request()?->ip(),
                'user_id' => auth()->id(),
            ]);
        });
    })->create();
