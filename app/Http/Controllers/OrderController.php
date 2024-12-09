<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ChecksBannedUsers;

class OrderController extends Controller
{
    use ChecksBannedUsers;

    public function index()
    {
        $this->checkIfBanned();
        $orders = Order::with(['items.product'])
            ->where('account_id', Auth::user()->account->account_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->order_id,
                    'orderNumber' => $order->order_number,
                    'totalAmount' => $order->total_amount,
                    'pickupTime' => $order->pickup_time->format('l, M j, Y - g:i A'),
                    'status' => ucfirst($order->status),
                    'createdAt' => $order->created_at->format('M j, Y, g:i A'),
                    'items' => $order->items->map(fn($item) => [
                        'name' => $item->product->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->price * $item->quantity,
                        'product_image_url' => $item->product->product_image_url
                    ]),
                    'note' => $order->note,
                    'canCancel' => $order->status === 'pending',
                ];
            });

        return Inertia::render('Order/History', [
            'orders' => $orders
        ]);
    }

    public function show(Order $order)
    {
        $this->checkIfBanned();
        $order->load(['items.product', 'account']);

        if ($order->account_id !== Auth::user()->account->account_id) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Order/Confirmation', [
            'order' => [
                'id' => $order->order_id,
                'orderNumber' => $order->order_number,
                'totalAmount' => $order->total_amount,
                'pickupTime' => $order->pickup_time->format('l, M j, Y - g:i A'),
                'status' => ucfirst($order->status),
                'note' => $order->note,
                'items' => $order->items->map(fn($item) => [
                    'name' => $item->product->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                    'product_image_url' => $item->product->product_image_url // Changed this line
                ])
            ]
        ]);
    }

    public function cancel(Order $order)
    {
        $this->checkIfBanned();
        if ($order->account_id !== Auth::user()->account->account_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending orders can be cancelled.']);
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully.');
    }
}