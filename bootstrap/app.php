<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

// $app->withMiddleware(function (Middleware $middleware) {
//     // Menambahkan middleware ke dalam global middleware stack
//     $middleware->append(RoleMiddleware::class);
// });
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(RoleMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
