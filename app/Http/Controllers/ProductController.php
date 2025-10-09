<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // 🛍️ Get all products (Frontend - Shop Page)
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->where('is_active', true);

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
        $sortBy = $request->get('sort_by', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_az':
                $query->orderBy('name', 'asc');
                break;
            case 'name_za':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        
        return view('frontend.products.index', compact('products'));
    }

    // 🧾 Show single product (Frontend - Product Detail Page)
    public function show($id)
    {
        $product = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->findOrFail($id);

        // Get related products from the same category
        $relatedProducts = Product::with(['images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }

    // 🌟 Featured products (Frontend)
    public function featured()
    {
        $products = Product::with(['category', 'images'])
            ->where('is_active', true)
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
            ->where('is_active', true)
            ->latest()
            ->take(10)
            ->get();

        return view('frontend.products.new-arrivals', compact('products'));
    }

    // 💸 On sale products (Frontend)
    public function onSale()
    {
        $products = Product::with(['category', 'images'])
            ->where('is_active', true)
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
            ->where('is_active', true)
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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
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

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

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

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $product->id,
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'description' => 'nullable|string',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

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
            'active_products' => Product::where('is_active', true)->count(),
            'inactive_products' => Product::where('is_active', false)->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            'low_stock' => Product::where('stock_quantity', '>', 0)
                                  ->where('stock_quantity', '<', 10)->count(),
            'featured_products' => Product::where('featured', true)->count(),
            'total_value' => Product::sum('price'),
        ];

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

        Product::whereIn('id', $request->product_ids)
               ->update(['is_active' => $request->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Products updated successfully'
        ], 200);
    }

    // 🛡️ Helper - Check admin access
    protected function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }
}