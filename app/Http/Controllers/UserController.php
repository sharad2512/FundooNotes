<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use App\Models\PasswordResetTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function signUp(Request $request)
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
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $user = User::find($request->input('user_id'));
        $status = Password::sendResetLink(['email' => $user->email]);
        if ($status === Password::RESET_LINK_SENT) {
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->update(['user_id' => $user->id]);
            // ->update(['user_id '=>$user->id ]);

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

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'newPassword' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $isValidate = PasswordResetToken::where('user_id', $request->user_id)->first();
        // ->where('token', PasswordResetToken->token)
        // ->exists();

        if (!$isValidate) {
            return response()->json(['message' => 'Invalid password reset token'], 401);
        }
        // Find the user by ID
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update the user's password
        $user->password = Hash::make($request->newPassword);
        if ($user->save()) {
            $isValidate->delete();
            return response(['message' => 'Password reset successful'], 200);
        }
    }
}
