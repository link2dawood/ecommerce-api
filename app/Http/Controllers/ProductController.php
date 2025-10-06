<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    // 🛍️ Get all products (with filters, search, etc.)
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'images', 'reviews'])
            ->where('status', 'active');

        // 🔍 Search by name or description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        // 🧭 Filter by category
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // 💰 Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // ⭐ Featured filter
        if ($request->has('featured')) {
            $query->where('featured', $request->featured);
        }

        // 📦 In stock filter
        if ($request->has('in_stock') && $request->in_stock) {
            $query->where('stock_quantity', '>', 0);
        }

        // 🔃 Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    // 🧾 Show single product
    public function show($id)
    {
        $product = Product::with([
            'categories',
            'images',
            'reviews' => function ($query) {
                $query->where('is_approved', true)
                      ->with('user:id,name')
                      ->latest()
                      ->limit(10);
            }
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->findOrFail($id);

        return response()->json($product, 200);
    }

    // 🌟 Featured products
    public function featured()
    {
        $products = Product::with(['categories', 'images'])
            ->where('status', 'active')
            ->where('featured', true)
            ->latest()
            ->take(10)
            ->get();

        return response()->json($products, 200);
    }

    // 🆕 New arrivals
    public function newArrivals()
    {
        $products = Product::with(['categories', 'images'])
            ->where('status', 'active')
            ->latest()
            ->take(10)
            ->get();

        return response()->json($products, 200);
    }

    // 💸 On sale products
    public function onSale()
    {
        $products = Product::with(['categories', 'images'])
            ->where('status', 'active')
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->latest()
            ->paginate(15);

        return response()->json($products, 200);
    }

    // ➕ Create product
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:price',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'vendor_id' => 'nullable|integer|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return response()->json($product->load('category'), 201);
    }

    // ✏️ Update product
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $product->id,
            'sku' => 'sometimes|required|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:price',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive',
            'featured' => 'boolean',
            'vendor_id' => 'nullable|integer|exists:users,id',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json($product->load('category'), 200);
    }

    // ❌ Delete product
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    // 🔍 Search products (frontend view)
    public function search(Request $request)
    {
        $query = $request->input('q');
        $products = Product::where('name', 'like', "%$query%")->get();
        return view('search', compact('products', 'query'));
    }

    // 🛡️ Helper - check admin access
    protected function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }
}
