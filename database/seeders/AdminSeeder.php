<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $user = User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'), // Change this password
        ]);

        // Create associated admin account
        Account::create([
            'email' => 'admin@example.com',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => Hash::make('admin123'), // Change this password
            'role' => 'admin',
            'is_customer' => false,
            'is_admin' => true,
            'is_staff' => false
        ]);
    }
}