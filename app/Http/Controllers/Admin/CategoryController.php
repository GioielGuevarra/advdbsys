<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->latest()
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->category_id,
                    'name' => $category->category_name,
                    'description' => $category->description,
                    'products_count' => $category->products_count,
                    'created_at' => $category->created_at->format('M j, Y')
                ];
            });

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories',
            'description' => 'required|string'
        ]);

        Category::create($validated);
        
        return back()->with('message', [
            'type' => 'success',
            'text' => 'Category created successfully'
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $category->category_id . ',category_id',
            'description' => 'required|string'
        ]);

        $category->update($validated);
        
        return back()->with('message', [
            'type' => 'success',
            'text' => 'Category updated successfully'
        ]);
    }

    public function destroy(Category $category)
    {
        try {
            if ($category->products()->exists()) {
                return back()->with('message', [
                    'type' => 'error',
                    'text' => 'Cannot delete category with associated products'
                ]);
            }

            $category->delete();
            return back()->with('message', [
                'type' => 'success',
                'text' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to delete category: ' . $e->getMessage());
            return back()->with('message', [
                'type' => 'error',
                'text' => 'Failed to delete category'
            ]);
        }
    }
}