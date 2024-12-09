<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class RedirectIfGuest
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}