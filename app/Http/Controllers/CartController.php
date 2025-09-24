<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ShopingCart;
use App\Models\Product;

class CartController extends Controller
{
     public function index()
    {
        $cartItems = ShoppingCart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json($cartItems, 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        $cartItem = ShoppingCart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = ShoppingCart::create([
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->price,
            ]);
        }

        return response()->json($cartItem->load('product'), 201);
    }




    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = ShoppingCart::where('user_id', Auth::id())
            ->findOrFail($id);

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json($cartItem->load('product'), 200);
    }





    public function destroy($id)
    {
        $cartItem = ShoppingCart::where('user_id', Auth::id())
            ->findOrFail($id);

        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed'], 200);
    }
}
