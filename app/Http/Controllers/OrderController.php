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

        // If web request, return view
        if (!request()->expectsJson()) {
            return view('frontend.orders.index', compact('orders'));
        }

        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::with(['items.product.images', 'items.product.categories'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // If web request, return view
        if (!request()->expectsJson()) {
            return view('frontend.orders.show', compact('order'));
        }

        return response()->json($order, 200);
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($order->status, ['pending', 'processing'])) {
            if (!request()->expectsJson()) {
                return back()->with('error', 'Cannot cancel this order');
            }
            return response()->json(['message' => 'Cannot cancel this order'], 400);
        }

        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            $order->status = 'cancelled';
            $order->payment_status = 'refunded';
            $order->save();

            DB::commit();

            if (!request()->expectsJson()) {
                return back()->with('success', 'Order cancelled successfully');
            }

            return response()->json($order, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (!request()->expectsJson()) {
                return back()->with('error', 'Failed to cancel order');
            }
            
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

        if (!request()->expectsJson()) {
            return view('frontend.orders.track', compact('order', 'timeline'));
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

        // If web request, return admin view
        if (!request()->expectsJson()) {
            return view('admin.orders.index', compact('orders'));
        }

        return response()->json($orders, 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $this->authorizeAdmin();

        $validator = \Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        if ($validator->fails()) {
            if (!request()->expectsJson()) {
                return back()->withErrors($validator)->withInput();
            }
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        
        // Update payment status based on order status
        if ($request->status === 'delivered') {
            $order->payment_status = 'paid';
        } elseif ($request->status === 'cancelled') {
            $order->payment_status = 'refunded';
        }
        
        $order->save();

        if (!request()->expectsJson()) {
            return back()->with('success', 'Order status updated successfully');
        }

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
            'payment_method' => 'nullable|in:cod,card,paypal',
        ]);

        $userId = Auth::id();
        $cartItems = ShoppingCart::with('product')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            if (!request()->expectsJson()) {
                return back()->with('error', 'Cart is empty');
            }
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();

        try {
            // Validate stock availability for all items
            foreach ($cartItems as $item) {
                $product = $item->product;
                if (!$product) {
                    DB::rollBack();
                    return back()->with('error', 'Some products in your cart no longer exist');
                }
                
                if ($product->stock_quantity < $item->quantity) {
                    DB::rollBack();
                    
                    if (!request()->expectsJson()) {
                        return back()->with('error', 'Insufficient stock for product: ' . $product->name);
                    }
                    
                    return response()->json([
                        'message' => 'Insufficient stock for product: ' . $product->name,
                        'product_id' => $product->id,
                        'available_stock' => $product->stock_quantity,
                        'requested_quantity' => $item->quantity
                    ], 400);
                }
            }

            // Calculate totals with current product prices
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $product = $item->product;
                $currentPrice = $product->sale_price ?? $product->price;
                $subtotal += $currentPrice * $item->quantity;
            }

            $taxAmount = 0; // Calculate tax if needed: $subtotal * 0.1 for 10% tax
            $shippingAmount = 0; // Add shipping cost if needed
            $discountAmount = 0; // Apply discount if needed
            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();

            // Create Order
            $order = Order::create([
                'order_number'     => $orderNumber,
                'user_id'          => $userId,
                'status'           => 'pending',
                'payment_status'   => 'pending',
                'payment_method'   => $request->payment_method ?? 'cod',
                'subtotal'         => $subtotal,
                'tax_amount'       => $taxAmount,
                'shipping_amount'  => $shippingAmount,
                'discount_amount'  => $discountAmount,
                'total_amount'     => $totalAmount,
                'currency'         => 'USD',
                'shipping_address' => $request->shipping_address,
                'billing_address'  => $request->billing_address ?? $request->shipping_address,
                'notes'            => $request->notes ?? null,
            ]);

            // Create Order Items and decrement stock
            foreach ($cartItems as $item) {
                $product = $item->product;
                $currentPrice = $product->sale_price ?? $product->price;
                $itemTotal = $currentPrice * $item->quantity;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $currentPrice,
                    'total'      => $itemTotal,
                ]);

                // Decrement stock
                $product->decrement('stock_quantity', $item->quantity);
            }

            // Clear cart
            ShoppingCart::where('user_id', $userId)->delete();

            DB::commit();

            // Send confirmation email
            try {
                SendOrderConfirmationEmail::dispatch($order);
            } catch (\Exception $e) {
                // Log email error but don't fail the order
                \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            }

            if (!request()->expectsJson()) {
                return redirect()->route('orders.show', $order->id)
                    ->with('success', 'Order placed successfully!');
            }

            return response()->json($order->load('items.product'), 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Order creation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (!request()->expectsJson()) {
                return back()->with('error', 'Order creation failed: ' . $e->getMessage());
            }
            
            return response()->json(['message' => 'Order creation failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate a unique order number
     */
    private function generateOrderNumber()
    {
        do {
            // Format: ORD-YYYYMMDD-RANDOM (e.g., ORD-20251010-ABC123)
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}