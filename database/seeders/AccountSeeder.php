<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create staff user and account 
        $staffUser = User::create([
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
        ]);

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
