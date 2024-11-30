<?php
namespace Database\Seeders;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Database\Seeder;

class ProductItemSeeder extends Seeder
{
    public function run(): void
    {
        // Add initial inventory for each product
        Product::all()->each(function ($product) {
            ProductItem::create([
                'product_id' => $product->product_id,
                'batch_number' => 'INIT' . str_pad($product->product_id, 4, '0', STR_PAD_LEFT),
                'expiration_date' => now()->addMonths(6),
                'quantity' => 100,
                'status' => 'in_stock',
                'added_at' => now()
            ]);
        });
    }
}