<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function create() {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request) 
    {
        $credentials = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed',
                'regex:/[a-z]/',    
                'regex:/[A-Z]/',    
                'regex:/[0-9]/',    
                'regex:/[@$!%*?&]/'
            ],
        ]);

        $user = User::create([
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        // Create associated account
        Account::create([
            'email' => $credentials['email'],
            'first_name' => $credentials['first_name'],
            'last_name' => $credentials['last_name'],
            'password' => Hash::make($credentials['password']), // Consider if you need password in Account
            'role' => 'customer',
            'is_customer' => true,
            'is_admin' => false,
            'is_staff' => false
        ]);

        event(new Registered($user));
        Auth::login($user);
        return redirect()->route('verification.notice');
    }
}
