<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        return Inertia::render('Category/Show', [
            'currentCategory' => $category->category_name,
            'products' => $category->products
            // No need to pass categories here as it's globally shared
        ]);
    }
}