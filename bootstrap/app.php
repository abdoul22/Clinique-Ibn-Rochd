<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middlewares globaux nécessaires pour Auth (session, cookies, etc.)
        $middleware->append([
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Middlewares d'alias utilisés dans les routes
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'is.approved' => \App\Http\Middleware\IsApproved::class,
            'update.lastlogin' => \App\Http\Middleware\UpdateLastLoginAt::class,
        ]);
    })
    ->booting(function (Application $app) {
        if ($app->environment('testing')) {
            // Empêcher les composants console d'afficher des confirmations en test
            putenv('SHELL_VERBOSITY=0');
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
