<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index($productId)
    {
        $reviews = Review::with('user:id,name')
            ->where('product_id', $productId)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        // Check if user has already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'You have already reviewed this product'], 400);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false, // Requires admin approval
        ]);

        return response()->json($review->load('user:id,name'), 201);
    }

    public function update(Request $request, $id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'sometimes|required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $review->update($request->only(['rating', 'comment']));
        $review->is_approved = false; // Reset approval status

        $review->save();

        return response()->json($review, 200);
    }

    public function destroy($id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully'], 200);
    }

    public function approve($id)
    {
        $this->authorizeAdmin();

        $review = Review::findOrFail($id);
        $review->is_approved = true;
        $review->save();

        return response()->json($review, 200);
    }

    public function getUserReviews()
    {
        $reviews = Review::with('product:id,name,image')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews, 200);
    }

    protected function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action');
        }
    }
}