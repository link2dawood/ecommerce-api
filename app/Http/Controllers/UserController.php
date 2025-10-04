<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        
        // If it's a web request, return view
        if (!request()->expectsJson()) {
            return view('frontend.profile', compact('user'));
        }
        
        return response()->json($user, 200);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            if (!request()->expectsJson()) {
                return back()->withErrors($validator)->withInput();
            }
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'email', 'phone']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        if (!request()->expectsJson()) {
            return back()->with('success', 'Profile updated successfully!');
        }

        return response()->json($user, 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if (!request()->expectsJson()) {
                return back()->withErrors($validator)->withInput();
            }
            return response()->json(['status_code' => 400, 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            if (!request()->expectsJson()) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        if (!request()->expectsJson()) {
            return back()->with('success', 'Password changed successfully!');
        }

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function deleteAccount()
    {
        $user = Auth::user();

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Delete user
        $user->delete();

        if (!request()->expectsJson()) {
            return redirect('/')->with('success', 'Account deleted successfully');
        }

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }

    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
            // FIXED: Changed 'total' to 'total_amount'
            'total_spent' => $user->orders()->where('status', 'completed')->sum('total_amount'),
            'wishlist_count' => \DB::table('wishlists')->where('user_id', $user->id)->count(),
            'cart_count' => \DB::table('shopping_carts')->where('user_id', $user->id)->count(),
            'reviews_count' => $user->reviews()->count(),
        ];

        $recentOrders = $user->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // If it's a web request, return view
        if (!request()->expectsJson()) {
            $recent_orders = $recentOrders; // Rename for blade compatibility
            return view('frontend.dashboard', compact('user', 'stats', 'recent_orders'));
        }

        return response()->json([
            'user' => $user,
            'stats' => $stats,
            'recent_orders' => $recentOrders,
        ], 200);
    }

    public function edit()
    {
        $user = Auth::user();
        return view('frontend.profile-edit', compact('user'));
    }
}