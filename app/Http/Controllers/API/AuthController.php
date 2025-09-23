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
      'name' => 'required',
      'email' => 'required',
      'password' => 'required'
     ]);
     if($validator-> fails()) {

        return response()->json(['status_code'=>400, 'message'=>'Bad Request']);
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
      
      'email' => 'required',
      'password' => 'required'
     ]);
     if($validator-> fails()) {

        return response()->json(['status_code'=>400, 'message'=>'Bad Request']);
     }
     
     $credentials = request(['email', 'password']);
     if(!Auth::attempt($credentials))
     {
        return response()->json(['status_code'=>500, 'message'=>'UnAuthorized']);
     }

     $user = User:: where('email', $request->email)->first();

     $tokenResult = $user->creatToken('authToken')->plainTextToken;
     return response()->json(['status_code'=>200, 'token'=> $tokenResult]);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['status_code'=>200, 'token'=> 'token Deleted']);
  }
}
