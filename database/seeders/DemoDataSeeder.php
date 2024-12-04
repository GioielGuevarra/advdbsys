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
        // Create 20 customer accounts
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

        // Create 50 orders distributed among customers
        $customers->each(function ($customer) {
            // Generate 1-5 orders per customer
            $numOrders = rand(1, 5);
            
            Order::factory()
                ->count($numOrders)
                ->create([
                    'account_id' => $customer->account_id
                ])
                ->each(function ($order) {
                    // Add 1-5 items to each order
                    $products = Product::inRandomOrder()->take(rand(1, 5))->get();
                    
                    $total = 0;
                    foreach ($products as $product) {
                        $quantity = rand(1, 3);
                        $price = $product->price;
                        
                        OrderItem::create([
                            'order_id' => $order->order_id,
                            'product_id' => $product->product_id,
                            'quantity' => $quantity,
                            'price' => $price
                        ]);
                        
                        $total += $price * $quantity;
                    }
                    
                    $order->update(['total_amount' => $total]);
                });
            
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