<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
class ProductController extends Controller
{
    
    public function index(Request $request)
    {
         $page = $request->get('page', 1);
    $perPage = $request->get('per_page', 15);
    $filtersHash = md5(json_encode($request->only(['q','category','sort','min_price','max_price'])));

    $cacheKey = "products:page:{$page}:per:{$perPage}:{$filtersHash}";

    // Use tags (works with redis/memcached)
    $products = Cache::tags(['products'])->remember($cacheKey, 60, function () use ($perPage) {
        return \App\Models\Product::with('images')
                ->where('status', 'active')
                ->paginate($perPage);
    });

   
    $query = Product::with('category');
    
    // Search by name
    if ($request->has('search') && !empty($request->search)) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $products = $query->paginate(10); // Paginate with 10 items per page
    return response()->json($products);
    }
  

   public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'slug'              => 'required|string|max:255|unique:products,slug',
            'sku'               => 'required|string|max:100|unique:products,sku',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lte:price',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string',
            'stock_quantity'    => 'required|integer|min:0',
            'min_stock_level'   => 'nullable|integer|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'dimensions'        => 'nullable|string',
            'status'            => 'required|in:active,inactive',
            'featured'          => 'boolean',
            'vendor_id'         => 'nullable|integer|exists:users,id',
            'category_id'       => 'required|exists:categories,id',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only([
            'name', 'slug', 'sku', 'price', 'sale_price', 'description',
            'short_description', 'stock_quantity', 'min_stock_level',
            'weight', 'dimensions', 'status', 'featured', 'vendor_id', 'category_id'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return response()->json($product->load('category'), 201);
    }




     public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'              => 'sometimes|required|string|max:255',
            'slug'              => 'sometimes|required|string|max:255|unique:products,slug,' . $product->id,
            'sku'               => 'sometimes|required|string|max:100|unique:products,sku,' . $product->id,
            'price'             => 'sometimes|required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lte:price',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string',
            'stock_quantity'    => 'sometimes|required|integer|min:0',
            'min_stock_level'   => 'nullable|integer|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'dimensions'        => 'nullable|string',
            'status'            => 'sometimes|required|in:active,inactive',
            'featured'          => 'boolean',
            'vendor_id'         => 'nullable|integer|exists:users,id',
            'category_id'       => 'sometimes|required|exists:categories,id',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only([
            'name', 'slug', 'sku', 'price', 'sale_price', 'description',
            'short_description', 'stock_quantity', 'min_stock_level',
            'weight', 'dimensions', 'status', 'featured', 'vendor_id', 'category_id'
        ]);

        // Replace image if uploaded
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json($product->load('category'), 200);
    }





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



    
}
