<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or user email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        
        return view('admin.orders.create', compact('users'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'nullable|string|max:50',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'shipping_address' => 'nullable|json',
            'billing_address' => 'nullable|json',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled,refunded',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);

        DB::beginTransaction();
        try {
            // Generate unique order number
            $validated['order_number'] = $this->generateOrderNumber();
            
            // Set defaults
            $validated['status'] = $validated['status'] ?? 'pending';
            $validated['payment_status'] = $validated['payment_status'] ?? 'pending';
            $validated['currency'] = $validated['currency'] ?? 'USD';
            $validated['tax_amount'] = $validated['tax_amount'] ?? 0;
            $validated['shipping_amount'] = $validated['shipping_amount'] ?? 0;
            $validated['discount_amount'] = $validated['discount_amount'] ?? 0;

            $order = Order::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.orders.show', $order->id)
                ->with('success', 'Order created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit($id)
    {
        $order = Order::with(['user', 'items'])->findOrFail($id);
        $users = User::orderBy('name')->get();
        
        return view('admin.orders.edit', compact('order', 'users'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled,refunded',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'payment_method' => 'nullable|string|max:50',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|json',
            'billing_address' => 'nullable|json',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $order->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.orders.show', $order->id)
                ->with('success', 'Order updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified order.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        DB::beginTransaction();
        try {
            $order->delete();

            DB::commit();

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        $order->update($validated);

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update($validated);

        return back()->with('success', 'Payment status updated successfully.');
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}