<?php
namespace Database\Factories;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'customer_name' => $this->faker->name(),
            'status' => $this->faker->randomElement(['pending', 'preparing', 'completed', 'cancelled']),
            'total_price' => $this->faker->randomFloat(2, 10, 200),
            'account_id' => null // Will be set when order is created through kiosk
        ];
    }
}