<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\CreateCooperativa::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(prepend: [
        \App\Http\Middleware\TenantMiddleware::class,
    ]);
        $middleware->alias([
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'apitoken' => \App\Http\Middleware\ApiTokenMiddleware::class,
            'api.token' => \App\Http\Middleware\ApiTokenMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
