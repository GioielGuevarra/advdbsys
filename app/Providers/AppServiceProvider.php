<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share categories data globally
        Inertia::share([
            'categories' => fn () => Category::all()->map(function ($category) {
                return [
                    'category_id' => $category->category_id,
                    'category_name' => $category->category_name
                ];
            })
        ]);
    }
}
