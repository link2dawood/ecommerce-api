<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get featured or latest products
        $featuredProducts = Product::where('status', 'active')
            ->where('featured', true)
            ->latest()
            ->take(8)
            ->get();

        // If no featured products, get latest products
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('status', 'active')
                ->latest()
                ->take(8)
                ->get();
        }

        // Get recent products
        $recentProducts = Product::where('status', 'active')
            ->latest()
            ->take(8)
            ->get();

        // ✅ Fixed: Use is_active for categories (not status)
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->take(10)
            ->get();

        return view('frontend.home', compact('featuredProducts', 'recentProducts', 'categories'));
    }
}
