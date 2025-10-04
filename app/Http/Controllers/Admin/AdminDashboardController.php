<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Build all stats
        $stats = [
            'total_orders'   => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'total_users'    => User::count(),
            'total_revenue'  => Order::where('status', 'completed')->sum('total_amount'), // change to your column name
        ];

        // Get recent orders (last 5 or 10)
        $recent_orders = Order::with('user')->latest()->take(5)->get();

        // Pass both to the view
        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }
}
