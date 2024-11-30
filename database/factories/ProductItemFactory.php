<?php
namespace Database\Factories;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductItemFactory extends Factory
{
    protected $model = ProductItem::class;

    public function definition()
    {
        return [
            'batch_number' => $this->faker->unique()->numerify('BATCH####'),
            'expiration_date' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'quantity' => $this->faker->numberBetween(10, 100),
            'status' => $this->faker->randomElement(['in_stock', 'low_stock', 'out_of_stock']),
            'added_at' => now()
        ];
    }
}