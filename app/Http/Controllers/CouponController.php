<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(15);
        return response()->json($coupons, 200);
    }

    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $coupon = Coupon::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'Invalid coupon code'], 404);
        }

        // Check expiration
        if ($coupon->valid_from && Carbon::parse($coupon->valid_from)->isFuture()) {
            return response()->json(['message' => 'Coupon is not yet valid'], 400);
        }

        if ($coupon->valid_until && Carbon::parse($coupon->valid_until)->isPast()) {
            return response()->json(['message' => 'Coupon has expired'], 400);
        }

        // Check usage limit
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return response()->json(['message' => 'Coupon usage limit reached'], 400);
        }

        // Check minimum purchase amount
        if ($coupon->min_purchase_amount && $request->total_amount < $coupon->min_purchase_amount) {
            return response()->json([
                'message' => 'Minimum purchase amount not met',
                'required_amount' => $coupon->min_purchase_amount
            ], 400);
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($request->total_amount * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount) {
                $discount = min($discount, $coupon->max_discount_amount);
            }
        } else {
            $discount = $coupon->discount_value;
        }

        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'discount_amount' => round($discount, 2),
            'final_amount' => round($request->total_amount - $discount, 2),
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $coupon = Coupon::create($request->all());

        return response()->json($coupon, 201);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $coupon = Coupon::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'discount_type' => 'sometimes|required|in:percentage,fixed',
            'discount_value' => 'sometimes|required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $coupon->update($request->all());

        return response()->json($coupon, 200);
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();

        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully'], 200);
    }

    protected function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action');
        }
    }
}