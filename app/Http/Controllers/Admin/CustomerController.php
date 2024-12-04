<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::with(['orders' => function($query) {
            $query->with('items.product')->latest();
        }])
        ->where('is_customer', true);

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()
            ->paginate(10)
            ->through(function($customer) {
                return [
                    'id' => $customer->account_id,
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $customer->email,
                    'joinedDate' => $customer->created_at->format('M j, Y'),
                    'totalOrders' => $customer->orders->count(),
                    'totalSpent' => $customer->orders->sum('total_amount'),
                    'status' => $customer->is_banned ? 'Banned' : 'Active',
                    'isBanned' => $customer->is_banned,
                    'orders' => $customer->orders->map(fn($order) => [
                        'id' => $order->order_id,
                        'orderNumber' => $order->order_number,
                        'amount' => $order->total_amount,
                        'status' => ucfirst($order->status),
                        'date' => $order->created_at->format('M j, Y g:i A'),
                        'items' => $order->items->map(fn($item) => [
                            'name' => $item->product->product_name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total' => $item->price * $item->quantity
                        ])
                    ])
                ];
            });

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search'])
        ]);
    }

    public function update(Request $request, Account $customer)
    {
        $validated = $request->validate([
            'action' => 'required|in:ban,unban'
        ]);

        $customer->update([
            'is_banned' => $validated['action'] === 'ban'
        ]);

        return back()->with('success', 
            $validated['action'] === 'ban' 
                ? 'Customer has been banned.' 
                : 'Customer has been unbanned.'
        );
    }

    private function getCustomerStatus($customer)
    {
        return $customer->is_banned ? 'Banned' : 'Active';
    }
}
