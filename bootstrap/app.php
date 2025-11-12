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
            'ensure.installed' => \App\Http\Middleware\EnsureSystemInstalled::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'permission.all' => \App\Http\Middleware\CheckAllPermissions::class,
            'set.locale' => \App\Http\Middleware\SetLocale::class,
        ]);
        
        // Добавляем middleware для логирования активности пользователей
        $middleware->append(\App\Http\Middleware\LogUserActivity::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
