<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Account;
use Inertia\Inertia;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current month's revenue
        $currentMonthRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Get last month's revenue
        $lastMonthRevenue = Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');

        // Calculate percentage change
        $revenueChange = $lastMonthRevenue ? 
            round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        // Get revenue data for the last 6 months
        $monthlyRevenue = Order::select(
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw("DATE_FORMAT(created_at, '%b') as month")
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('created_at')
            ->get();

        $recentOrders = Order::with(['account', 'items.product'])
            ->select([
                'orders.*',
                DB::raw('CONCAT(accounts.first_name, " ", accounts.last_name) as customer_name')
            ])
            ->join('accounts', 'orders.account_id', '=', 'accounts.account_id')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->order_id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total_amount' => $order->total_amount,
                    'status' => ucfirst($order->status),
                    'date' => $order->created_at->format('M j, Y g:i A'),
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'revenue' => Order::sum('total_amount'),
                'revenueChange' => $revenueChange,
                'orders' => Order::count(),
                'products' => Product::count(),
                'users' => Account::where('is_customer', true)->count(),
            ],
            'monthlyRevenue' => $monthlyRevenue,
            'recentOrders' => $recentOrders,
            'recentCustomers' => Account::where('is_customer', true)
                ->latest()
                ->take(5)
                ->get()
        ]);
    }
}