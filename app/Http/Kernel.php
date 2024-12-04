<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ...existing code...

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,  // Add this line
        // ...existing code...
    ];

    protected $routeMiddleware = [
        // ...existing code...
    ];
}