<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                DB::rollBack();
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
                'shipping_address' => $validated['address'],
                'billing_address' => $validated['address'],
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
            if ($validated['payment_method'] === 'card') {
                // STRIPE PAYMENT
                Stripe::setApiKey(config('services.stripe.secret'));
                
                // Prepare line items for Stripe
                $lineItems = [];
                foreach ($cartItems as $item) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $item->product->name,
                                'description' => $item->product->description ?? '',
                            ],
                            'unit_amount' => intval($item->price * 100), // Convert to cents
                        ],
                        'quantity' => $item->quantity,
                    ];
                }

                // Add shipping if applicable
                if ($shippingAmount > 0) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Shipping',
                            ],
                            'unit_amount' => intval($shippingAmount * 100),
                        ],
                        'quantity' => 1,
                    ];
                }

                // Add tax if applicable
                if ($taxAmount > 0) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Tax',
                            ],
                            'unit_amount' => intval($taxAmount * 100),
                        ],
                        'quantity' => 1,
                    ];
                }
                
                // Create Stripe Checkout Session
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
                        'user_id' => Auth::id(),
                    ],
                ]);
                
                // Save Stripe session ID to order
                $order->update(['stripe_session_id' => $session->id]);
                
                DB::commit();
                
                // Redirect to Stripe Checkout
                return redirect($session->url);
                
            } else {
                // CASH ON DELIVERY
                $order->update([
                    'payment_status' => 'pending',
                    'status' => 'confirmed',
                ]);

                // Clear cart for COD orders
                ShoppingCart::where('user_id', Auth::id())->delete();
                
                DB::commit();

                return redirect()->route('checkout.success', ['order' => $order->id])
                    ->with('success', 'Order placed successfully!');
            }
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to process order. Please try again.');
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

    /**
     * Stripe Webhook Handler
     */
    public function webhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
            
            // Handle the event
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    
                    $order = Order::where('stripe_session_id', $session->id)->first();
                    
                    if ($order) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                            'stripe_payment_intent_id' => $session->payment_intent ?? null,
                        ]);

                        // Clear cart after successful payment
                        ShoppingCart::where('user_id', $order->user_id)->delete();
                    }
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    
                    $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();
                    
                    if ($order) {
                        $order->update([
                            'payment_status' => 'failed',
                            'status' => 'cancelled',
                        ]);
                    }
                    break;
                    
                default:
                    Log::info('Unhandled Stripe webhook event: ' . $event->type);
            }
            
            return response()->json(['status' => 'success'], 200);
            
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Stripe webhook invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
            
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Stripe webhook invalid signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
            
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Order success page
     */
    public function success(Request $request, $orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        
        // Ensure user can only view their own order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        // Verify Stripe payment if session_id exists
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
                    
                    // Clear cart after successful payment verification
                    ShoppingCart::where('user_id', Auth::id())->delete();
                }
            } catch (\Exception $e) {
                Log::error('Stripe session verification failed', [
                    'order_id' => $order->id,
                    'session_id' => $request->session_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('frontend.checkout.success', compact('order'));
    }
}