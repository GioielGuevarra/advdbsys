<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            AdminSeeder::class,     // Create admin first
            AccountSeeder::class,   // Then create staff
            DemoDataSeeder::class,  // Then create customer data
            ProductItemSeeder::class,
        ]);
    }
}
