<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // 🛍️ Get all products (Frontend - Shop Page)
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        // Only show active products on frontend (removed ->where('is_active', true))
        // This will be handled by the model's global scope if needed

        // 🔍 Search by name or description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // 🧭 Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 💰 Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 📦 In stock filter
        if ($request->has('in_stock') && $request->in_stock) {
            $query->where('stock_quantity', '>', 0);
        }

        // 🔃 Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_asc':
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
            case 'name_az':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
            case 'name_za':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        
        // Get categories with product count for sidebar
        $categories = Category::withCount('products')->get();
        
        // Get wishlist product IDs for authenticated user
        $wishlistProductIds = [];
        if (Auth::check()) {
            $wishlistProductIds = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }
        
        return view('frontend.products.index', compact('products', 'categories', 'wishlistProductIds'));
    }

    // 🧾 Show single product (Frontend - Product Detail Page)
    public function show($id)
    {
        $product = Product::with(['images', 'category'])->findOrFail($id);

        // Get related products from the same category
        $relatedProducts = Product::with(['images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('stock_quantity', '>', 0)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        // Check if product is in user's wishlist
        $isInWishlist = false;
        $relatedWishlistIds = [];
        
        if (Auth::check()) {
            $isInWishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
            
            // Get wishlist IDs for related products
            $relatedWishlistIds = Wishlist::where('user_id', Auth::id())
                ->whereIn('product_id', $relatedProducts->pluck('id'))
                ->pluck('product_id')
                ->toArray();
        }

        return view('frontend.products.show', compact('product', 'relatedProducts', 'isInWishlist', 'relatedWishlistIds'));
    }

    // 🌟 Featured products (Frontend)
    public function featured()
    {
        $products = Product::with(['category', 'images'])
            ->where('featured', true)
            ->latest()
            ->take(10)
            ->get();

        return view('frontend.products.featured', compact('products'));
    }

    // 🆕 New arrivals (Frontend)
    public function newArrivals()
    {
        $products = Product::with(['category', 'images'])
            ->latest()
            ->take(10)
            ->get();

        return view('frontend.products.new-arrivals', compact('products'));
    }

    // 💸 On sale products (Frontend)
    public function onSale()
    {
        $products = Product::with(['category', 'images'])
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->paginate(12);

        return view('frontend.products.on-sale', compact('products'));
    }

    // 🔍 Search products (Frontend)
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        $products = Product::with(['category', 'images'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%");
            })
            ->paginate(12);
            
        return view('frontend.products.search', compact('products', 'query'));
    }

    // ========================================
    // ADMIN API ENDPOINTS
    // ========================================

    // 📋 Admin: Get all products (API)
    public function apiIndex(Request $request)
    {
        $this->authorizeAdmin();

        $query = Product::with(['category', 'images']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status (only if is_active column exists)
        if ($request->filled('status')) {
            try {
                $query->where('is_active', $request->status == 'active');
            } catch (\Exception $e) {
                // Skip if column doesn't exist
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    // ➕ Admin: Create product (API)
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'featured' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        // Only validate is_active if column exists
        if (\Schema::hasColumn('products', 'is_active')) {
            $rules['is_active'] = 'boolean';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load('category')
        ], 201);
    }

    // ✏️ Admin: Update product (API)
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $product = Product::findOrFail($id);

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $product->id,
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'description' => 'nullable|string',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'featured' => 'boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        // Only validate is_active if column exists
        if (\Schema::hasColumn('products', 'is_active')) {
            $rules['is_active'] = 'boolean';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load('category')
        ], 200);
    }

    // ❌ Admin: Delete product (API)
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $product = Product::findOrFail($id);

        // Delete product image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete all product images if using ProductImage model
        if ($product->images) {
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->url)) {
                    Storage::disk('public')->delete($image->url);
                }
            }
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }

    // 📊 Admin: Get product statistics
    public function statistics()
    {
        $this->authorizeAdmin();

        $stats = [
            'total_products' => Product::count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            'low_stock' => Product::where('stock_quantity', '>', 0)
                                  ->where('stock_quantity', '<', 10)->count(),
            'total_value' => Product::sum('price'),
        ];

        // Add is_active stats only if column exists
        if (\Schema::hasColumn('products', 'is_active')) {
            $stats['active_products'] = Product::where('is_active', true)->count();
            $stats['inactive_products'] = Product::where('is_active', false)->count();
        }

        // Add featured stats only if column exists
        if (\Schema::hasColumn('products', 'featured')) {
            $stats['featured_products'] = Product::where('featured', true)->count();
        }

        return response()->json($stats);
    }

    // 🔄 Admin: Bulk update status
    public function bulkUpdateStatus(Request $request)
    {
        $this->authorizeAdmin();

        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Only update if is_active column exists
        if (\Schema::hasColumn('products', 'is_active')) {
            Product::whereIn('id', $request->product_ids)
                   ->update(['is_active' => $request->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Products updated successfully'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'is_active column does not exist'
        ], 400);
    }

    // 🛡️ Helper - Check admin access
    protected function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }
}