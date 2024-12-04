<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'items']);

        if ($request->filled('search')) {
            $query->where('product_name', 'like', "%{$request->search}%");
        }

        // Only apply category filter if a specific category is selected
        if ($request->filled('category') && $request->category !== 'null') {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.category_id', $request->category);
            });
        }

        $products = $query->latest()
            ->paginate(100)
            ->through(fn($product) => [
                'id' => $product->product_id,
                'name' => $product->product_name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => asset('storage/' . $product->product_image), // Add asset() helper
                'categories' => $product->categories->map(fn($category) => [
                    'id' => $category->category_id,
                    'name' => $category->category_name,
                ]),
                'stock' => $product->items->sum('quantity'),
                'status' => $product->items->count() > 0 ? 'In Stock' : 'Out of Stock'
            ]);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'category']),
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Product store request:', $request->all()); // Add logging

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'image' => 'required|image|max:2048', // 2MB max
            'quantity' => 'required|integer|min:0',
            'expiration_date' => 'required|date|after:today',
        ]);

        // Convert category IDs to integers
        $categoryIds = array_map('intval', $validated['categories']);

        $imagePath = $request->file('image')->store('products', 'public');

        // Start transaction
        \DB::beginTransaction();

        try {
            // Create product
            $product = Product::create([
                'product_name' => $validated['product_name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'product_image' => $imagePath
            ]);

            // Attach categories
            $product->categories()->attach($categoryIds);

            // Create initial inventory
            ProductItem::create([
                'product_id' => $product->product_id,
                'batch_number' => 'BTH-' . strtoupper(str_pad($product->product_id, 6, '0', STR_PAD_LEFT)),
                'expiration_date' => $validated['expiration_date'],
                'quantity' => $validated['quantity'],
                'status' => 'in_stock',
                'added_at' => now()
            ]);

            \DB::commit();
            return redirect()->back()->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            \DB::rollBack();
            Storage::disk('public')->delete($imagePath);
            return redirect()->back()->with('error', 'Failed to create product');
        }
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        \DB::beginTransaction();

        try {
            if ($request->hasFile('image')) {
                // Delete old image
                Storage::disk('public')->delete($product->product_image);
                $validated['product_image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($validated);
            $product->categories()->sync($validated['categories']);

            \DB::commit();
            return redirect()->back()->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update product');
        }
    }

    public function destroy(Product $product)
    {
        try {
            Storage::disk('public')->delete($product->product_image);
            $product->delete();
            return redirect()->back()->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete product');
        }
    }

    public function updateInventory(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'expiration_date' => 'required|date|after:today',
        ]);

        try {
            ProductItem::create([
                'product_id' => $product->product_id,
                'batch_number' => 'BTH-' . strtoupper(str_pad($product->product_id, 6, '0', STR_PAD_LEFT)) . '-' . now()->format('Ymd'),
                'expiration_date' => $validated['expiration_date'],
                'quantity' => $validated['quantity'],
                'status' => 'in_stock',
                'added_at' => now()
            ]);

            return redirect()->back()->with('success', 'Inventory updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update inventory');
        }
    }
}