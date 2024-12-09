<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Traits\ChecksBannedUsers;

class CartController extends Controller
{
    use ChecksBannedUsers;

    public function addToCart(Request $request)
    {
        $this->checkIfBanned();
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,product_id',
                'quantity' => 'required|integer|min:1'
            ]);
            
            $cart = Cart::firstOrCreate(
                [
                    'account_id' => Auth::user()->account->account_id,
                    'status' => 'active'
                ]
            );

            $existingItem = CartItem::where('cart_id', $cart->cart_id)
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($existingItem) {
                $existingItem->quantity = $validated['quantity'];
                $existingItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->cart_id,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity']
                ]);
            }

            return back()->with('success', 'Added to cart');
        } catch (\Exception $e) {
            Log::error('Cart error: ' . $e->getMessage());
            return back()->with('error', 'Could not add to cart');
        }
    }

    public function removeFromCart(CartItem $cartItem)
    {
        $this->checkIfBanned();
        try {
            $cartItem->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Cart remove error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not remove item'], 500);
        }
    }

    public function getCart()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $cart = Cart::with(['items.product'])
                ->where('account_id', Auth::user()->account->account_id)
                ->where('status', 'active')
                ->first();

            // If request wants JSON (from axios/fetch)
            if (request()->wantsJson()) {
                if ($cart) {
                    // Make sure product image URLs are included
                    $cart->items->each(function ($item) {
                        $item->product->product_image_url = $item->product->product_image_url;
                    });
                }
                return response()->json($cart ?? ['items' => []]);
            }

            // Otherwise return Inertia view
            return Inertia::render('Cart/Show', [
                'cart' => $cart
            ]);
        } catch (\Exception $e) {
            Log::error('Get cart error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not fetch cart'], 500);
        }
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $this->checkIfBanned();
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $cartItem->update([
                'quantity' => $validated['quantity']
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Cart update error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not update cart'], 500);
        }
    }

    public function checkout()
    {
        $this->checkIfBanned();
        try {
            $cart = Cart::with(['items.product'])
                ->where('account_id', Auth::user()->account->account_id)
                ->where('status', 'active')
                ->firstOrFail();

            // Get available pickup time slots for the current day
            $pickupSlots = $this->generatePickupSlots();

            return Inertia::render('Checkout/Show', [
                'cart' => $cart,
                'pickupSlots' => $pickupSlots,
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Could not proceed to checkout');
        }
    }

    public function processCheckout(Request $request)
    {
        $this->checkIfBanned();
        try {
            Log::info('Starting checkout process', ['request' => $request->all()]);

            $validated = $request->validate([
                'pickup_time' => [
                    'required',
                    'date',
                    'after:now',
                    function ($attribute, $value, $fail) {
                        // Convert UTC time to Manila time for validation
                        $pickupTime = \Carbon\Carbon::parse($value)
                            ->timezone('Asia/Manila');
                        
                        $maxDate = now()
                            ->timezone('Asia/Manila')
                            ->addDay()
                            ->setHour(17)
                            ->setMinute(0)
                            ->setSecond(0);
                        
                        if ($pickupTime->greaterThan($maxDate)) {
                            $fail('Pickup time must be within next business day before 5 PM.');
                        }
                        
                        // Check business hours in Manila time
                        $hour = $pickupTime->hour;
                        if ($hour < 9 || $hour >= 17) {
                            $fail('Pickup time must be between 9 AM and 5 PM.');
                        }
                    }
                ],
                'note' => 'nullable|string|max:500'
            ]);

            // Get active cart
            $cart = Cart::with(['items.product'])
                ->where('account_id', Auth::user()->account->account_id)
                ->where('status', 'active')
                ->firstOrFail();

            DB::beginTransaction(); // Start transaction

            try {
                // Convert pickup time to Asia/Manila before storing
                $pickupTime = \Carbon\Carbon::parse($validated['pickup_time'])
                    ->timezone('Asia/Manila')
                    ->format('Y-m-d H:i:s');

                // Create order with Manila time
                $order = Order::create([
                    'account_id' => Auth::user()->account->account_id,
                    'total_amount' => $this->calculateTotal($cart),
                    'pickup_time' => $pickupTime, // Store as Manila time
                    'note' => $validated['note'],
                    'status' => 'pending'
                ]);

                Log::info('Order created', ['order_id' => $order->order_id]);

                // Move cart items to order items
                foreach ($cart->items as $item) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price
                    ]);
                }

                // Clear the cart
                $cart->items()->delete();
                $cart->delete();

                DB::commit(); // Commit transaction

                Log::info('Checkout completed successfully', ['order_id' => $order->order_id]);

                // Trigger cart-updated event through response headers
                return redirect()
                    ->route('order.history')
                    ->with('success', 'Order placed successfully! View your order details below.')
                    ->header('X-Trigger-Cart-Update', 'true');
                    
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback on error
                Log::error('Transaction failed: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Checkout failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withErrors(['error' => 'Could not process checkout: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function generatePickupSlots()
    {
        $slots = [];
        $currentTime = now()->timezone('Asia/Manila');
        $businessStartHour = 9;
        $businessEndHour = 17;
        
        if ($currentTime->hour >= $businessEndHour) {
            // Show tomorrow's slots
            $tomorrowStart = $currentTime->copy()
                ->addDay()
                ->setTimezone('Asia/Manila')
                ->startOfDay()
                ->setHour($businessStartHour);
            $tomorrowEnd = $tomorrowStart->copy()->setHour($businessEndHour);
            
            for ($date = $tomorrowStart; $date->lt($tomorrowEnd); $date->addHour()) {
                $slots[] = [
                    'value' => $date->copy()->setTimezone('UTC')->toISOString(),
                    'label' => $date->format('l, M j - g:i A')
                ];
            }
        } else {
            // Show today's slots starting from business hours
            $startDate = $currentTime->copy()
                ->setTimezone('Asia/Manila')
                ->startOfDay()
                ->setHour($businessStartHour);
                
            if ($currentTime->hour >= $businessStartHour) {
                $startDate = $currentTime->copy()
                    ->setTimezone('Asia/Manila')
                    ->addHour()
                    ->startOfHour();
            }
            
            $endDate = $currentTime->copy()
                ->setTimezone('Asia/Manila')
                ->setHour($businessEndHour);
            
            for ($date = $startDate; $date->lt($endDate); $date->addHour()) {
                $slots[] = [
                    'value' => $date->copy()->setTimezone('UTC')->toISOString(),
                    'label' => $date->format('l, M j - g:i A')
                ];
            }
        }
        
        return $slots;
    }

    private function calculateTotal($cart)
    {
        return $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }
}