<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['account', 'items.product']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('account', function($subq) use ($search) {
                      $subq->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
                  });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()
            ->paginate(10)
            ->through(function($order) {
                $customerName = $order->account 
                    ? $order->account->first_name . ' ' . $order->account->last_name
                    : 'Unknown Customer';

                return [
                    'id' => $order->order_id,
                    'orderNumber' => $order->order_number,
                    'customer' => $customerName,
                    'amount' => $order->total_amount,
                    'status' => $order->status,
                    'pickupTime' => $order->pickup_time?->format('M j, Y g:i A') ?? 'N/A',
                    'items' => $order->items->map(function($item) {
                        $product = $item->product;
                        return [
                            'name' => $product ? $product->product_name : 'Deleted Product',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total' => $item->price * $item->quantity,
                        ];
                    }),
                    'note' => $order->note ?? '',
                    'createdAt' => $order->created_at->format('M j, Y g:i A'),
                ];
            });

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        // Prevent status change if order is completed or cancelled
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot modify completed or cancelled orders');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,completed,cancelled'
        ]);

        // Start transaction
        DB::beginTransaction();

        try {
            // Handle stock changes based on status transition
            if ($validated['status'] === 'completed') {
                // Deduct stock when completing order
                foreach ($order->items as $item) {
                    ProductItem::deductStock($item->product, $item->quantity);
                }
            }

            $order->update($validated);
            DB::commit();

            return back()->with('success', 'Order status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }
}
