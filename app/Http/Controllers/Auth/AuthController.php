<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            // Generate token
            $token = $user->createToken('AuthToken')->accessToken;
    
            return response()->json([
                'message' => 'User registered successfully',
                'token' => $token,
                'user' => $user
            ], 201);
            
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        // dd($user);
        $token = $user->createToken('AuthToken')->accessToken;
        return response()->json(['token' => $token], 200);
    }

    public function dashboard()
    {
        return response()->json(['message' => 'Welcome to Dashboard!', 'user' => Auth::user()]);
    }

    public function logout()
    {
        $user = Auth::user();
        
        /** @var \App\Models\User $user */
        $user->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

}
