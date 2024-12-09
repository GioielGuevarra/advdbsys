<?php
namespace Database\Factories;

use App\Models\Order;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $statuses = ['pending', 'preparing', 'ready', 'completed', 'cancelled'];
        $status = $this->faker->randomElement($statuses);
        
        return [
            'order_number' => 'ORD-' . strtoupper($this->faker->bothify('########')),
            'account_id' => Account::factory(),
            'total_amount' => $this->faker->randomFloat(2, 20, 500),
            'pickup_time' => $this->faker->dateTimeBetween('now', '+2 days'),
            'note' => $this->faker->optional(0.3)->sentence(),
            'status' => $status,
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now')
        ];
    }
}