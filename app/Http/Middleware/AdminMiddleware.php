<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Add debugging
        Log::info('AdminMiddleware check', [
            'user' => $user?->email,
            'account' => $user?->account,
            'is_admin' => $user?->account?->is_admin,
            'role' => $user?->account?->role
        ]);
        
        if (!$user || !$user->account || !in_array($user->account->role, ['admin', 'staff'])) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}