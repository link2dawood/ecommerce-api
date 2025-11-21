<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

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

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        // Get applied coupon from session
        $appliedCoupon = Session::get('applied_coupon');
        $discountAmount = 0;
        
        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            } else {
                Session::forget('applied_coupon');
                $appliedCoupon = null;
            }
        }

        $total = $subtotal - $discountAmount;

        return view('frontend.checkout.index', compact('cartItems', 'subtotal', 'discountAmount', 'total', 'appliedCoupon'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $cartItems = ShoppingCart::where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        if (!$coupon->isValid($subtotal)) {
            $message = 'This coupon is not valid';
            
            if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
                $message = 'Minimum purchase amount of $' . number_format($coupon->min_purchase, 2) . ' required';
            } elseif ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                $message = 'This coupon has reached its usage limit';
            } elseif (!$coupon->is_active) {
                $message = 'This coupon is no longer active';
            }
            
            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        }

        $discountAmount = $coupon->calculateDiscount($subtotal);
        $total = $subtotal - $discountAmount;

        Session::put('applied_coupon', $coupon->code);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => number_format($discountAmount, 2),
            'total' => number_format($total, 2),
            'coupon_code' => $coupon->code,
            'coupon_type' => $coupon->type,
            'coupon_value' => $coupon->value
        ]);
    }

    public function removeCoupon()
    {
        Session::forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully'
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:stripe,paypal',
        ]);

        DB::beginTransaction();
        
        try {
            $cartItems = ShoppingCart::where('user_id', Auth::id())
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty');
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item->quantity * $item->price;
            }

            $taxAmount = 0;
            $shippingAmount = 0;
            $discountAmount = 0;
            $couponCode = null;

            // Apply coupon if exists
            $appliedCoupon = Session::get('applied_coupon');
            if ($appliedCoupon) {
                $coupon = Coupon::where('code', $appliedCoupon)->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                }
            }

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
                'shipping_address' => $validated['address'],
                'billing_address' => $validated['address'],
                'coupon_code' => $couponCode,
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

            // Handle Payment Method
            if ($validated['payment_method'] === 'stripe') {
                // STRIPE PAYMENT
                Stripe::setApiKey(config('services.stripe.secret'));
                
                $lineItems = [];
                foreach ($cartItems as $item) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $item->product->name,
                            ],
                            'unit_amount' => intval($item->price * 100),
                        ],
                        'quantity' => $item->quantity,
                    ];
                }

                if ($discountAmount > 0) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Discount (' . $couponCode . ')',
                            ],
                            'unit_amount' => -intval($discountAmount * 100),
                        ],
                        'quantity' => 1,
                    ];
                }
                
                $session = StripeSession::create([
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => route('checkout.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('checkout.index') . '?canceled=1',
                    'customer_email' => Auth::user()->email,
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ],
                ]);
                
                $order->update(['stripe_session_id' => $session->id]);
                
                DB::commit();
                
                // Increment coupon usage
                if ($couponCode) {
                    $coupon->incrementUsage();
                    Session::forget('applied_coupon');
                }
                
                return redirect($session->url);
                
            } else {
                // PAYPAL PAYMENT
                $order->update(['payment_status' => 'pending']);
                
                DB::commit();

                // Increment coupon usage
                if ($couponCode) {
                    $coupon->incrementUsage();
                    Session::forget('applied_coupon');
                }

                // Redirect to PayPal
                return redirect()->route('checkout.paypal', ['order' => $order->id]);
            }
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to process order. Please try again.');
        }
    }

    public function paypal($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('frontend.checkout.paypal', compact('order'));
    }

    public function paypalExecute(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Update order status
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        // Clear cart
        ShoppingCart::where('user_id', Auth::id())->delete();

        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function success(Request $request, $orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        if ($request->has('session_id') && $order->stripe_session_id) {
            Stripe::setApiKey(config('services.stripe.secret'));
            
            try {
                $session = StripeSession::retrieve($request->session_id);
                
                if ($session->payment_status === 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                        'stripe_payment_intent_id' => $session->payment_intent ?? null,
                    ]);
                    
                    ShoppingCart::where('user_id', Auth::id())->delete();
                }
            } catch (\Exception $e) {
                Log::error('Stripe session verification failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('frontend.checkout.success', compact('order'));
    }
}