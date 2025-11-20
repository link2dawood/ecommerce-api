<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function session(Request $request)
    {
        // 1. Validate that the user actually has a cart/product to pay for
        // $totalPrice = $request->get('total_price'); // Example

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Total E-commerce Order', // You can make this dynamic
                    ],
                    'unit_amount' => 5000, // Amount in cents (5000 = $50.00). Replace with your cart total * 100
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // These redirect to the files shown in your screenshot
            'success_url' => route('checkout.success'), 
            'cancel_url' => route('checkout.index'),
        ]);

        return redirect()->away($session->url);
    }

    public function success()
    {
        // Here you can empty the cart and mark the order as "Paid" in your database
        return view('frontend.checkout.success');
    }
}