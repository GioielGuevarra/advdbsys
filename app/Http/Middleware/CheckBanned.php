<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->account && Auth::user()->account->is_banned) {
            // Add flash message about account being banned
            session()->flash('warning', 'Your account has been banned. You can browse but cannot place orders.');
        }

        return $next($request);
    }
}