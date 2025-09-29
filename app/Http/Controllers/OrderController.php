<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ShoppingCart;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendOrderConfirmationEmail;

class OrderController extends Controller
{
    
     public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
        ]);

        $userId = Auth::id();
        $cartItems = ShoppingCart::with('product')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();

        try {
            // Validate stock availability for all items
            foreach ($cartItems as $item) {
                $product = $item->product;
                if ($product->stock_quantity < $item->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Insufficient stock for product: ' . $product->name,
                        'product_id' => $product->id,
                        'available_stock' => $product->stock_quantity,
                        'requested_quantity' => $item->quantity
                    ], 400);
                }
            }

            // Calculate total with current product prices
            $total = 0;
            foreach ($cartItems as $item) {
                $product = $item->product;
                $currentPrice = $product->sale_price ?? $product->price;
                $total += $currentPrice * $item->quantity;
            }

            // Create Order
            $order = Order::create([
                'user_id'          => $userId,
                'total'            => $total,
                'status'           => 'pending',
                'shipping_address' => $request->shipping_address,
            ]);

            // Create Order Items and decrement stock
            foreach ($cartItems as $item) {
                $product = $item->product;
                $currentPrice = $product->sale_price ?? $product->price;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $currentPrice,
                ]);

                // Decrement stock
                $product->decrement('stock_quantity', $item->quantity);
            }


            ShoppingCart::where('user_id', $userId)->delete();

            DB::commit();

               SendOrderConfirmationEmail::dispatch($order);

            return response()->json($order->load('items.product'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Order creation failed', 'error' => $e->getMessage()], 500);
        }

    }    

}
