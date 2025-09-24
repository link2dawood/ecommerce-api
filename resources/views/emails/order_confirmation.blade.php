<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
        <div style="background: #4CAF50; padding: 20px; color: white; text-align: center;">
            <h2>Thank You for Your Order!</h2>
        </div>
        <div style="padding: 20px;">
            <p>Hello <strong>{{ $order->user->name }}</strong>,</p>
            <p>We’ve received your order <strong>#{{ $order->id }}</strong> and it’s currently being processed.</p>

            <h3>Order Details</h3>
            <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f1f1f1;">
                        <th align="left">Product</th>
                        <th align="center">Qty</th>
                        <th align="right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td align="center">{{ $item->quantity }}</td>
                            <td align="right">${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin-top: 15px;">
                <strong>Total:</strong> ${{ number_format($order->total, 2) }}
            </p>

            <h3>Shipping Address</h3>
            <p>{{ $order->shipping_address }}</p>

            <p style="margin-top: 20px;">
                We’ll notify you once your order has been shipped.  
                Thank you for shopping with us!
            </p>
        </div>
        <div style="background: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #555;">
            &copy; {{ date('Y') }} Your Store. All rights reserved.
        </div>
    </div>
</body>
</html>
