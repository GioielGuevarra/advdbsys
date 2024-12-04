<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        try {
            // Get monthly revenue data
            $monthlyRevenue = Order::select(
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month")
            )
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('created_at')
                ->get();

            // Get top selling products with product names
            $topProducts = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.product_id')
                ->select(
                    'products.product_name as name',
                    DB::raw('SUM(order_items.quantity) as total_sold'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
                )
                ->groupBy('products.product_id', 'products.product_name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();

            // Get sales by category
            $categoryStats = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.product_id')
                ->join('product_categories', 'products.product_id', '=', 'product_categories.product_id')
                ->join('categories', 'product_categories.category_id', '=', 'categories.category_id')
                ->select(
                    'categories.category_name as name',
                    DB::raw('SUM(order_items.quantity) as total_sold'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
                )
                ->groupBy('categories.category_id', 'categories.category_name')
                ->orderByDesc('revenue')
                ->limit(5)
                ->get();

            // Get top customers by order value
            $topCustomers = DB::table('orders')
                ->join('accounts', 'orders.account_id', '=', 'accounts.account_id')
                ->select(
                    DB::raw('CONCAT(accounts.first_name, " ", accounts.last_name) as name'),
                    DB::raw('COUNT(orders.order_id) as total_orders'),
                    DB::raw('SUM(orders.total_amount) as total_spent')
                )
                ->groupBy('accounts.account_id', 'accounts.first_name', 'accounts.last_name')
                ->orderByDesc('total_spent')
                ->limit(5)
                ->get();

        } catch (\Exception $e) {
            \Log::error('Analytics query error: ' . $e->getMessage());
            return Inertia::render('Admin/Analytics/Index', [
                'monthlyRevenue' => [],
                'topProducts' => [],
                'categoryStats' => [],
                'topCustomers' => [],
                'error' => 'Failed to load analytics data'
            ]);
        }

        return Inertia::render('Admin/Analytics/Index', [
            'monthlyRevenue' => $monthlyRevenue,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'topCustomers' => $topCustomers
        ]);
    }
}