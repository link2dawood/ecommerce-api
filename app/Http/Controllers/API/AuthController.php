<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
     $validator = validator:: make($request->all(),[
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => 'required|string|min:8|confirmed'
     ]);
     if($validator-> fails()) {

        return response()->json(['status_code'=>400, 'message'=>'Validation failed', 'errors' => $validator->errors()], 400);
     }

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = bcrypt($request->password);
    $user->save();


    return response()->json(['status_code'=>200, 'message'=>'User Created Successfully']);
}
  
   public function login(Request $request)
    {
     $validator = validator:: make($request->all(),[

      'email' => 'required|string|email',
      'password' => 'required|string'
     ]);
     if($validator-> fails()) {

        return response()->json(['status_code'=>400, 'message'=>'Validation failed', 'errors' => $validator->errors()], 400);
     }

     $credentials = request(['email', 'password']);
     if(!Auth::attempt($credentials))
     {
        return response()->json(['status_code'=>401, 'message'=>'Invalid credentials'], 401);
     }

     $user = User:: where('email', $request->email)->first();

     $tokenResult = $user->createToken('authToken')->plainTextToken;
     return response()->json(['status_code'=>200, 'token'=> $tokenResult]);
  }

  public function logout(Request $request)
{
    if ($request->user() && $request->user()->currentAccessToken()) {
        $request->user()->currentAccessToken()->delete();
    }

    \Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('status', 'Logged out successfully!');
}

}
