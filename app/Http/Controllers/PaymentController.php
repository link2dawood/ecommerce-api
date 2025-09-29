<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Process payment for an order
     * This is a placeholder for payment gateway integration
     * You can integrate Stripe, PayPal, or other payment gateways
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:credit_card,debit_card,paypal,stripe,cash_on_delivery',
            'payment_details' => 'nullable|array',
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($request->order_id);

        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Order already paid'], 400);
        }

        DB::beginTransaction();
        try {
            // Process payment based on method
            switch ($request->payment_method) {
                case 'cash_on_delivery':
                    $order->payment_method = 'cash_on_delivery';
                    $order->payment_status = 'pending';
                    break;

                case 'stripe':
                case 'paypal':
                case 'credit_card':
                case 'debit_card':
                    // Here you would integrate with actual payment gateway
                    // For now, we'll simulate successful payment
                    $order->payment_method = $request->payment_method;
                    $order->payment_status = 'paid';
                    $order->status = 'processing';
                    break;

                default:
                    return response()->json(['message' => 'Invalid payment method'], 400);
            }

            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Payment processed successfully',
                'order' => $order,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Payment processing failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get payment methods
     */
    public function paymentMethods()
    {
        $methods = [
            ['id' => 'credit_card', 'name' => 'Credit Card', 'icon' => 'credit-card'],
            ['id' => 'debit_card', 'name' => 'Debit Card', 'icon' => 'credit-card'],
            ['id' => 'paypal', 'name' => 'PayPal', 'icon' => 'paypal'],
            ['id' => 'stripe', 'name' => 'Stripe', 'icon' => 'stripe'],
            ['id' => 'cash_on_delivery', 'name' => 'Cash on Delivery', 'icon' => 'cash'],
        ];

        return response()->json($methods, 200);
    }

    /**
     * Verify payment (webhook endpoint for payment gateways)
     */
    public function verifyPayment(Request $request)
    {
        // This is a placeholder for payment gateway webhook
        // Each gateway has its own webhook verification process

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'transaction_id' => 'required|string',
            'status' => 'required|in:success,failed',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($request->status === 'success') {
            $order->payment_status = 'paid';
            $order->transaction_id = $request->transaction_id;
            $order->status = 'processing';
        } else {
            $order->payment_status = 'failed';
        }

        $order->save();

        return response()->json(['message' => 'Payment verified'], 200);
    }

    /**
     * Refund payment
     */
    public function refund($orderId)
    {
        $this->authorizeAdmin();

        $order = Order::findOrFail($orderId);

        if ($order->payment_status !== 'paid') {
            return response()->json(['message' => 'Cannot refund unpaid order'], 400);
        }

        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($order->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }

            $order->payment_status = 'refunded';
            $order->status = 'cancelled';
            $order->save();

            DB::commit();

            return response()->json(['message' => 'Refund processed successfully', 'order' => $order], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Refund failed'], 500);
        }
    }

    protected function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action');
        }
    }
}