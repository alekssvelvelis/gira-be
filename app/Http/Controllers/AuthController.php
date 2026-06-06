<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'nickname' => 'nullable|string|max:16|min:4|unique'
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'nickname' => $validated['nickname']
        ]);

        Log::info('User created', ['user_id' => $user->id, 'email' => $user->email]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully', 
            'user' => $user,
            'token' => $token
            ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'User logged out',
        ], 201);
    }
}
