<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ...existing code...

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,  
    ];

    protected $routeMiddleware = [
        'check.banned' => \App\Http\Middleware\CheckBanned::class,
        'guest.redirect' => \App\Http\Middleware\RedirectIfGuest::class,
    ];
}