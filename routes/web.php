<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/category/{category}', [ProductController::class, 'byCategory'])->name('category.show');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// Auth routes
require __DIR__.'/auth.php';

// Customer routes
Route::middleware(['auth'])->group(function() {
    // profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    //order 
    Route::get('/order/{order}/confirmation', [OrderController::class, 'show'])->name('order.confirmation');
    
    // orders
    Route::get('/orders', [OrderController::class, 'index'])->name('order.history');
    Route::delete('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Cart routes
    Route::get('cart', [CartController::class, 'getCart'])->name('cart.get');
    Route::post('cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::patch('cart/{cartItem}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('cart/{cartItem}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    
    // Checkout routes
    Route::get('checkout', [CartController::class, 'checkout'])->name('checkout.show');
    Route::post('checkout', [CartController::class, 'processCheckout'])->name('checkout.process');
});

// Admin routes
require __DIR__.'/admin.php';
