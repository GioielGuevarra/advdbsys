<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request) {
        return Inertia::render('Profile/Edit', [
            'user' => $request->user(),
            'status' => session('status')
        ]);
    }

    // Remove updateInfo method as it's no longer needed

    public function updatePassword(Request $request) {
        $fields = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8', 
                'regex:/[a-z]/', 
                'regex:/[A-Z]/', 
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/'
            ]
        ]);

        $request->user()->update([
            'password' => Hash::make($fields['password'])
        ]);

        return redirect()->route('profile.edit');
    }

    public function destroy(Request $request) {
        $request->validate([
            'password' => ['required', 'current_password']
        ]);

        $user = $request->user();
        
        Auth::logout();

        // The delete method will now handle nulling sensitive data and soft deleting
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
