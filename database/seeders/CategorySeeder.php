<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // define
        $categories = [
            ['category_name' => 'Classic', 'description' => 'Traditional favorites everyone loves'],
            ['category_name' => 'Gourmet', 'description' => 'Premium and specialty items'],
            ['category_name' => 'Bars and Squares', 'description' => 'Delightful baked treats'],
            ['category_name' => 'Sundaes', 'description' => 'Ice cream specialties'],
            ['category_name' => 'Milkshakes', 'description' => 'Creamy blended beverages'],
            ['category_name' => 'Gift Box', 'description' => 'Perfect for special occasions']
        ];

        // insert to category table
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
