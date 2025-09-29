<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->with('parent')
            ->orderBy('name')
            ->get();

        return response()->json($categories, 200);
    }

    public function show($id)
    {
        $category = Category::with(['products' => function($query) {
            $query->where('status', 'active')->limit(20);
        }, 'children'])->findOrFail($id);

        return response()->json($category, 200);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'slug', 'description', 'parent_id', 'is_active']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'slug', 'description', 'parent_id', 'is_active']);

        if ($request->hasFile('image')) {
            if ($category->image) {
                \Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return response()->json($category, 200);
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();

        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with products'], 400);
        }

        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

    protected function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action');
        }
    }
}