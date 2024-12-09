<?php
namespace Database\Seeders;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductItemSeeder extends Seeder
{
    public function run(): void
    {
        // Get random dates within this week
        $startDate = now();
        $endDate = now()->endOfWeek();

        Product::all()->each(function ($product) use ($startDate, $endDate) {
            ProductItem::create([
                'product_id' => $product->product_id,
                'batch_number' => 'INIT' . str_pad($product->product_id, 4, '0', STR_PAD_LEFT),
                'expiration_date' => Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                ),
                'quantity' => 100,
                'status' => 'in_stock',
                'added_at' => now()
            ]);
        });
    }
}