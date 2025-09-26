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
        then: function () {
            Route::middleware('web')
                ->prefix('api')
                ->group(base_path('routes/sanctum.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Only apply stateful middleware to specific routes that need it
        $middleware->web(append: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // Register custom middleware aliases
        $middleware->alias([
            'check.setting' => \App\Http\Middleware\CheckOrganizationSettings::class,
            'check.maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
