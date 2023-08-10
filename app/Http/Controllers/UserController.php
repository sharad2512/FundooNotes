<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }
    // user login function

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials ', 'credentials' => $credentials], 401);
        }
        $token = Auth::fromUser(Auth::user());
        return response()->json(compact('token'));
    }
    //Show user details function
    public function userDetails()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'user not authenticated'], 401);
        }
        return response()->json(['user' => $user]);
    }
    // user logout function
    public function logout()
    {
        if (Auth::check()) {
            echo Auth::user();
            Auth::logout();

            return response()->json(['message' => 'Logout successful'], 200);
        } else {
            return response()->json(['message' => 'User is not logged in'], 401);
        }
    }
    //forgot-password function 
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent to your email'], 200);
        } elseif ($status === Password::INVALID_USER) {
            return response()->json(['message' => 'Invalid email address'], 400);
        } elseif ($status === Password::RESET_THROTTLED) {
            return response()->json(['message' => 'Password reset request throttled'], 429);
        } else
            return response()->json(['message' => 'Unable to send reset link'], 400);
    }

    //Reset password fuction 
    public function resetPassword(Request $request)
    {   //validation for inputs
        
    //    $validator1= $request->validate([
    //     'email'=>'required|string|email',
    //     'password'=>'required|string|confirmed',
    //     // 'token'=>'required',
    //    ]);
       $validator = Validator::make($request->all(),[
        'email'=>'required|string|email',
        'password'=>'required|string|confirmed',
        // 'token'=>'required'
       ]);
       if ($validator->fails()) {
          return response()->json($validator->errors());
       }

       $passwordResetTokens = PasswordResetTokens::where('email',$request->email)
       ->where('token',$request->token)->first();
       
       if (!$passwordResetTokens) {
        return response()->json(['message'=>'Invalid password reset token'],401);
       }
       
       $user = User::where('email',$passwordResetTokens->email)->first();
      
       if (!$user) {
        return response()->json(['message'=>'User not found'],404);
       }
       
       $user->password = Hash::make($request->password);
       $user->save();
       $passwordResetTokens->delete();
      
       return response(['message'=> 'Password reset successfull'],200);

    }
}
