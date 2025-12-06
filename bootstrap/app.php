<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
            'school' => \App\Http\Middleware\CheckSchool::class,
            'master.admin' => \App\Http\Middleware\CheckMasterAdmin::class,
            'inventory.admin' => \App\Http\Middleware\CheckInventoryAdmin::class,
            'master.admin.guest' => \App\Http\Middleware\RedirectIfMasterAdmin::class,
            'inventory.admin.guest' => \App\Http\Middleware\RedirectIfInventoryAdmin::class,
        ]);
        $middleware->redirectTo(
            guests: '/parent/login',
            users: '/parent/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
