<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     */
    protected $middleware = [
        // Your existing middleware
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // Your existing web middleware
        ],
        'api' => [
            // Your existing API middleware
        ],
    ];

    /**
     * The application's middleware aliases.
     * 
     * Aliases may be used instead of class names to conveniently
     * assign middleware to routes and groups.
     */
    protected $middlewareAliases = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        // Your other middleware aliases
        'role' => \App\Http\Middleware\RoleMiddleware::class, // Make sure this line exists
    ];
}