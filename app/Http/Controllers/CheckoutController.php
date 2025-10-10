<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cartItems = ShoppingCart::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return view('frontend.checkout.index', compact('cartItems', 'total'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,card',
        ]);

        DB::beginTransaction();
        
        try {
            // Get cart items
            $cartItems = ShoppingCart::where('user_id', Auth::id())
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty');
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item->quantity * $item->price;
            }

            $taxAmount = 0; // You can calculate tax if needed
            $shippingAmount = 0; // You can add shipping cost if needed
            $discountAmount = 0; // You can apply discount if needed
            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'currency' => 'USD',
                'shipping_address' => $validated['address'], // Will be cast to JSON automatically
                'billing_address' => $validated['address'], // Will be cast to JSON automatically
                'notes' => null,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $itemTotal = $cartItem->quantity * $cartItem->price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $itemTotal,
                ]);
            }

            // Clear cart
            ShoppingCart::where('user_id', Auth::id())->delete();

            DB::commit();

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Order placed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to process order. Error: ' . $e->getMessage());
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

    public function success($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        
        // Ensure user can only view their own order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        return view('frontend.checkout.success', compact('order'));
    }
}