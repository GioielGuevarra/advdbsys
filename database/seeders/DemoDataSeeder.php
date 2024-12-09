<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory;

class DemoDataSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function run()
    {
        // Create customers first
        $customers = $this->createCustomers();
        
        // Generate orders with realistic patterns
        $this->generateOrders($customers);
        
        // Create active carts for some customers
        $this->createActiveCarts($customers);
    }

    protected function createCustomers()
    {
        $customers = collect();
        for ($i = 1; $i <= 20; $i++) {
            $email = "customer{$i}@example.com";
            
            // Create user
            $user = User::create([
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
            
            // Create account
            $account = Account::create([
                'email' => $email,
                'first_name' => $this->faker->firstName(),
                'last_name' => $this->faker->lastName(),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_admin' => false,
                'is_staff' => false,
                'is_customer' => true
            ]);
            
            $customers->push($account);
        }
        return $customers;
    }

    protected function generateOrders($customers)
    {
        $startDate = now()->subMonths(12)->startOfDay();
        $endDate = now()->endOfDay();
        $baseOrdersPerDay = 8;
        $monthlyGrowthRate = 1.05;
        
        $currentDate = $startDate;
        
        while ($currentDate <= $endDate) {
            $monthsSinceStart = $startDate->diffInMonths($currentDate);
            $growthMultiplier = pow($monthlyGrowthRate, $monthsSinceStart);
            $dayOfWeekMultiplier = ($currentDate->isWeekend()) ? 1.5 : 1.0;
            $targetOrders = round($baseOrdersPerDay * $growthMultiplier * $dayOfWeekMultiplier);
            $randomMultiplier = $this->faker->randomFloat(2, 0.8, 1.2);
            $ordersToCreate = round($targetOrders * $randomMultiplier);
            
            for ($i = 0; $i < $ordersToCreate; $i++) {
                DB::transaction(function () use ($customers, $currentDate) {
                    $customer = $customers->random();
                    $orderTime = $currentDate->copy()->addSeconds(rand(0, 86399));
                    
                    // Create order items first to calculate total
                    $items = [];
                    $total = 0;
                    $itemCount = rand(1, 5);
                    $products = Product::inRandomOrder()->take($itemCount)->get();
                    
                    foreach ($products as $product) {
                        $quantity = $this->faker->randomElement([1, 1, 1, 2, 2, 3]);
                        $price = $product->price;
                        $items[] = [
                            'product_id' => $product->product_id,
                            'quantity' => $quantity,
                            'price' => $price
                        ];
                        $total += $price * $quantity;
                    }

                    // Create order with total amount and pickup time
                    $order = Order::create([
                        'account_id' => $customer->account_id,
                        'order_number' => 'ORD-' . strtoupper($this->faker->bothify('??####')),
                        'status' => $this->faker->randomElement(['pending', 'preparing', 'ready', 'completed', 'cancelled']), // Updated with correct enum values
                        'total_amount' => $total,
                        'pickup_time' => $orderTime->copy()->addHours(rand(1, 4)), // Pickup time 1-4 hours after order
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);

                    // Create order items
                    foreach ($items as $item) {
                        OrderItem::create([
                            'order_id' => $order->order_id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price']
                        ]);
                    }
                });
            }
            
            $currentDate->addDay();
        }
    }

    protected function addOrderItems($order)
    {
        $total = 0;
        $itemCount = rand(1, 5);
        $products = Product::inRandomOrder()->take($itemCount)->get();
        
        foreach ($products as $product) {
            $quantity = $this->faker->randomElement([1, 1, 1, 2, 2, 3]); // More likely to order less quantity
            $price = $product->price;
            
            OrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'price' => $price
            ]);
            
            $total += $price * $quantity;
        }
        
        return $total;
    }

    protected function createActiveCarts($customers)
    {
        $customers->each(function ($customer) {
            // Create active cart for some customers (70% chance)
            if (rand(1, 100) <= 70) {
                $cart = Cart::create([
                    'account_id' => $customer->account_id,
                    'status' => 'active'
                ]);
                
                // Add 1-3 items to cart
                $products = Product::inRandomOrder()->take(rand(1, 3))->get();
                foreach ($products as $product) {
                    CartItem::create([
                        'cart_id' => $cart->cart_id,
                        'product_id' => $product->product_id,
                        'quantity' => rand(1, 2)
                    ]);
                }
            }
        });
    }
}