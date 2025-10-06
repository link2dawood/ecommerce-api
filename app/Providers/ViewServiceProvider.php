<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Share categories with all views
        View::composer('*', function ($view) {
            $categories = Category::with('children')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            
            $view->with('categories', $categories);
        });
    }
}