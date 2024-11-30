<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin account
        Account::create([
            'email' => 'admin@example.com',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_admin' => true,
            'is_staff' => false,
            'is_customer' => false
        ]);

        // Create default staff account
        Account::create([
            'email' => 'staff@example.com',
            'first_name' => 'Staff',
            'last_name' => 'User',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_admin' => false,
            'is_staff' => true,
            'is_customer' => false
        ]);
    }
}
