<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   
    public function register(RegisterRequest $request)
    {
        // dd($request->all());

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = auth('api')->login($user);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed, please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login(LoginRequest $request)
    {
        // return $request->all();

        $credentials = $request->validated();
        // dd($credentials);

        if (!$token = auth('api')->attempt($credentials)) {
            // dd('not match');
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

   
    public function profile()
    {
        // dd(auth('api')->user());
        return response()->json([
            'user' => auth('api')->user()
        ]);
    }
}
