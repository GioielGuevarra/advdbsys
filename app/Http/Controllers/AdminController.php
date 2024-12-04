<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'revenue' => Order::sum('total_amount'),
                'orders' => Order::count(),
                'products' => Product::count(),
                'users' => Account::where('is_customer', true)->count(),
            ],
            'recentOrders' => Order::with('account')
                ->latest()
                ->take(5)
                ->get(),
            'recentCustomers' => Account::where('is_customer', true)
                ->latest()
                ->take(5)
                ->get()
        ]);
    }

    public function orders()
    {
        return Inertia::render('Admin/Orders');
    }

    public function products()
    {
        return Inertia::render('Admin/Products');
    }

    public function categories()
    {
        return Inertia::render('Admin/Categories');
    }

    public function customers()
    {
        return Inertia::render('Admin/Customers');
    }

    public function analytics()
    {
        return Inertia::render('Admin/Analytics');
    }

}