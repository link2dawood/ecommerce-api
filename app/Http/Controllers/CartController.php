<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $cartItems = ShoppingCart::with('product.images')
                ->where('user_id', Auth::id())
                ->get();
        } else {
            // Handle guest cart using session
            $cart = session()->get('cart', []);
            $cartItems = collect();
            
            foreach ($cart as $id => $details) {
                $product = Product::with('images')->find($id);
                if ($product) {
                    $cartItems->push((object)[
                        'id' => $id,
                        'product' => $product,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'],
                    ]);
                }
            }
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->price;
        });

        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;

        return view('frontend.cart.index', compact('cartItems', 'subtotal', 'tax', 'total'));
    }

    public function add(Request $request, $id = null)
    {
        $productId = $id ?? $request->product_id;
        $quantity = $request->quantity ?? 1;

        $product = Product::findOrFail($productId);

        // Check stock availability
        if ($product->stock_quantity < $quantity) {
            return back()->with('error', 'Insufficient stock available. Only ' . $product->stock_quantity . ' items left.');
        }

        if (Auth::check()) {
            // Authenticated user - save to database
            $cartItem = ShoppingCart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                
                if ($product->stock_quantity < $newQuantity) {
                    return back()->with('error', 'Insufficient stock available.');
                }

                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                ShoppingCart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }
        } else {
            // Guest user - save to session
            $cart = session()->get('cart', []);

            if (isset($cart[$productId])) {
                $newQuantity = $cart[$productId]['quantity'] + $quantity;
                
                if ($product->stock_quantity < $newQuantity) {
                    return back()->with('error', 'Insufficient stock available.');
                }

                $cart[$productId]['quantity'] = $newQuantity;
            } else {
                $cart[$productId] = [
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ];
            }

            session()->put('cart', $cart);
        }

        return back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (Auth::check()) {
            $cartItem = ShoppingCart::where('user_id', Auth::id())
                ->findOrFail($id);

            $product = Product::findOrFail($cartItem->product_id);

            if ($product->stock_quantity < $request->quantity) {
                return back()->with('error', 'Insufficient stock available.');
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->save();
        } else {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$id])) {
                $product = Product::findOrFail($id);
                
                if ($product->stock_quantity < $request->quantity) {
                    return back()->with('error', 'Insufficient stock available.');
                }

                $cart[$id]['quantity'] = $request->quantity;
                session()->put('cart', $cart);
            }
        }

        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove($id)
    {
        if (Auth::check()) {
            $cartItem = ShoppingCart::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();
            
            $cartItem->delete();
        } else {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
        }

        return back()->with('success', 'Item removed from cart!');
    }

    public function clear()
    {
        if (Auth::check()) {
            ShoppingCart::where('user_id', Auth::id())->delete();
        } else {
            session()->forget('cart');
        }

        return back()->with('success', 'Cart cleared successfully!');
    }
}