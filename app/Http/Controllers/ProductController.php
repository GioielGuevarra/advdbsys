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
    public function index(Request $request)
    {
        $query = Product::with(['categories']);

        if ($request->filled('search')) {
            $query->where('product_name', 'like', "%{$request->search}%")
                  ->orWhereHas('categories', function($q) use ($request) {
                      $q->where('category_name', 'like', "%{$request->search}%");
                  });
        }

        $products = $query->latest()
            ->get()
            ->map(fn($product) => [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'description' => $product->description,
                'price' => $product->price,
                'product_image' => $product->product_image, // Changed to match ItemCard expectations
                'categories' => $product->categories->map(fn($category) => [
                    'category_id' => $category->category_id,
                    'category_name' => $category->category_name
                ])
            ]);

        $categories = Category::all();

        return Inertia::render('Home', [
            'products' => $products,
            'categories' => $categories,
            'heroImage' => asset('storage/images/hero.jpg'), // Updated path
            'searchTerm' => $request->search
        ]);
    }

    public function show(Product $product)
    {
        $product->load('categories');
        
        // Add the image URL to the product data
        $product->product_image_url = $product->product_image_url;

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
