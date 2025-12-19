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
            'admin.back_to_school' => \App\Http\Middleware\EnsureBackToSchoolAdmin::class,
            'admin.merchandise' => \App\Http\Middleware\EnsureMerchandiseAdmin::class,
            'guest.user' => \App\Http\Middleware\EnsureGuestUser::class,
            'store.admin.guest' => \App\Http\Middleware\RedirectIfStoreAdmin::class,
            'parent' => \App\Http\Middleware\EnsureParent::class,
        ]);
        $middleware->redirectTo(
            guests: function (\Illuminate\Http\Request $request) {
                if ($request->is('MasterAdmin') || $request->is('MasterAdmin/*')) {
                    return route('master.admin.login');
                }
                if ($request->is('InventoryAdmin') || $request->is('InventoryAdmin/*')) {
                    return route('inventory.admin.login');
                }
                if ($request->is('StoreAdmin') || $request->is('StoreAdmin/*')) {
                    return route('store.admin.login');
                }
                if ($request->is('BackToSchoolAdmin') || $request->is('BackToSchoolAdmin/*')) {
                    return route('store.admin.login');
                }
                if ($request->is('MerchandiseAdmin') || $request->is('MerchandiseAdmin/*')) {
                    return route('store.admin.login');
                }
                return route('login');
            },
            users: '/parent/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
