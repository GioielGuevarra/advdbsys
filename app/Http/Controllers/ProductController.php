<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['categories'])
            ->latest()
            ->get();

        $categories = Category::all();

        return Inertia::render('Home', [
            'products' => $products,
            'categories' => $categories,
            'heroImage' => asset('storage/images/hero.jpg'), // Updated path
        ]);
    }

    public function show(Product $product)
    {
        $product->load('categories');

        $relatedProducts = Product::whereHas('categories', function ($query) use ($product) {
            $query->whereIn('categories.category_id', $product->categories->pluck('category_id'));
        })
        ->where('products.product_id', '!=', $product->product_id)
        ->limit(4)
        ->get();

        return Inertia::render('Product/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function byCategory($category)
    {
        $products = Product::whereHas('categories', function ($query) use ($category) {
            $query->where('category_name', $category);
        })->with('categories')->get();

        return Inertia::render('Category/Show', [
            'products' => $products,
            'currentCategory' => $category
        ]);
    }
}
