<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Http\Controllers\CartController;
use App\Models\User;

use function Laravel\Prompts\error;

class AuthenticateController extends Controller
{
    public function create() {
        return Inertia::render('Auth/Login', [
            'status' => session('status')
        ]);
    }

    public function store(Request $request) 
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            if (!$user || !$user->account) {
                Auth::logout();
                return back()->withErrors([
                    'failed' => 'Account not found.'
                ]);
            }

            // Add debugging
            Log::info('User login attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->account->is_admin,
                'is_staff' => $user->account->is_staff,
                'role' => $user->account->role
            ]);

            // Make role check more explicit
            if ($user->account->role === 'admin' || $user->account->role === 'staff') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'failed' => 'Invalid credentials.'
        ]);
    }

    public function destroy(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // clear cart state on client side
        return redirect('/')->with('cart_reset', true);
    }
}
