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
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::with(['items.product.images', 'items.product.categories'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($order, 200);
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Cannot cancel this order'], 400);
        }

        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($order->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }

            $order->status = 'cancelled';
            $order->save();

            DB::commit();

            return response()->json($order, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to cancel order'], 500);
        }
    }

    public function track($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->findOrFail($id);

        $timeline = [
            ['status' => 'pending', 'label' => 'Order Placed', 'completed' => true],
            ['status' => 'processing', 'label' => 'Processing', 'completed' => in_array($order->status, ['processing', 'shipped', 'delivered'])],
            ['status' => 'shipped', 'label' => 'Shipped', 'completed' => in_array($order->status, ['shipped', 'delivered'])],
            ['status' => 'delivered', 'label' => 'Delivered', 'completed' => $order->status === 'delivered'],
        ];

        if ($order->status === 'cancelled') {
            $timeline = [
                ['status' => 'pending', 'label' => 'Order Placed', 'completed' => true],
                ['status' => 'cancelled', 'label' => 'Cancelled', 'completed' => true],
            ];
        }

        return response()->json([
            'order' => $order,
            'timeline' => $timeline,
        ], 200);
    }

    // Admin endpoints
    public function adminIndex()
    {
        $this->authorizeAdmin();

        $orders = Order::with(['user:id,name,email', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($orders, 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $this->authorizeAdmin();

        $validator = \Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json($order, 200);
    }

    protected function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action');
        }
    }

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
