<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with('product.images')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($wishlist, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Check if already in wishlist
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Product already in wishlist'], 400);
        }

        $wishlistItem = Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        return response()->json($wishlistItem->load('product'), 201);
    }

    public function destroy($id)
    {
        $wishlistItem = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $id)
            ->first();

        if (!$wishlistItem) {
            return response()->json(['message' => 'Product not in wishlist'], 404);
        }

        $wishlistItem->delete();

        return response()->json(['message' => 'Removed from wishlist'], 200);
    }

    public function clear()
    {
        Wishlist::where('user_id', Auth::id())->delete();

        return response()->json(['message' => 'Wishlist cleared'], 200);
    }
}