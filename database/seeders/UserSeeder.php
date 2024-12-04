<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Create associated test account
        Account::create([
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_customer' => true,
            'is_admin' => false,
            'is_staff' => false
        ]);
    }
}
