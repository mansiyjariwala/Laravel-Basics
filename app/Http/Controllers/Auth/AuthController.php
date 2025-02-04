<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\DashboardResource;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        // Start database transaction
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Handle both single role and multiple roles
            $roleInput = $request->roles;
            if (!is_array($roleInput)) {
                // If single role is provided as string, convert to array
                $roleInput = [$roleInput];
            }

            // Get all requested roles
            $roles = Role::whereIn('name', $roleInput)->get();

            // Verify if all requested roles exist
            if ($roles->count() !== count($roleInput)) {
                DB::rollBack();
                return response()->json(['error' => 'One or more invalid roles specified'], 400);
            }

            // Attach roles to user
            $user->roles()->attach($roles->pluck('id')->toArray());

            // Generate token
            $token = $user->createToken('AuthToken')->accessToken;

            // If everything is successful, commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'User registered successfully',
                'token' => $token,
                'user' => $user->load('roles')
            ], 201);

        } catch (\Throwable $e) {
            // If there's an error, rollback the transaction
            DB::rollBack();
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

        $roleNames = $user->roles()->pluck('name')->toArray();

        $token = $user->createToken('AuthToken')->accessToken;
        return response()->json(['token' => $token,'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $roleNames // Returns an array of roles
        ]], 200);
    }

    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return new DashboardResource($user);
    }

    public function logout()
    {
        $user = Auth::user();

        /** @var \App\Models\User $user */
        $user->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

}
