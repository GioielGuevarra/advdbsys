<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\AnalyticsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Remove duplicate routes and use resource properly
        Route::resource('orders', OrderController::class)->only(['index', 'update']);
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('customers', CustomerController::class);
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
        
        Route::post('products/{product}/inventory', [ProductController::class, 'updateInventory'])
            ->name('products.inventory.update');
        
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
    });