<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register a new user (API + Web)
     * For Postman: POST /api/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            return back()->withErrors($validator)->withInput();
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'user';
        $user->save();

        // Create token for API usage
        $token = $user->createToken('authToken')->plainTextToken;

        // API Response
        if ($request->expectsJson()) {
            return response()->json([
                'status_code' => 201,
                'message' => 'Registration successful',
                'user' => $user,
                'token' => $token
            ]);
        }

        // Web redirect
        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    /**
     * API Login
     * For Postman: POST /api/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status_code' => 200,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Web Login (Session-based)
     * For Web: POST /login
     */
    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect by role
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    /**
     * Admin Login (API + Web)
     * For Postman: POST /api/admin/login
     * For Web: POST /admin/login
     */
    public function adminLogin(Request $request)
    {
        if ($request->expectsJson()) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $credentials = $request->only(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = Auth::user();

            if ($user->role !== 'admin') {
                Auth::logout();
                return response()->json([
                    'status_code' => 403,
                    'message' => 'Unauthorized. Admin access only.'
                ], 403);
            }

            $token = $user->createToken('adminAuthToken')->plainTextToken;

            return response()->json([
                'status_code' => 200,
                'message' => 'Admin login successful',
                'user' => $user,
                'token' => $token
            ]);
        }

        // Web form submission
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->with('error', 'Unauthorized access. Admin only.');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->with('error', 'Invalid credentials');
    }

    /**
     * Logout (API + Web)
     * For Postman: POST /api/logout
     */
    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            if ($request->user() && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Logged out successfully'
            ]);
        }

        // Web logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Logged out successfully!');
    }
}
