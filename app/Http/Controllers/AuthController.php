<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        $role = User::where('email', $request->only('email'))->first()->role;

        if(!$token) {
            return response()->json([
                'message' => 'username or password wrong'
            ], 401);
        }

        return response()->json([
            'message' => 'login successfully.',
            'token' => $token,
            'role' => $role,
        ], 200);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'buyer'
        ];

        $user = User::create($data);
        
        return response()->json([
            'code' => 201,
            'status'=> 'registered',
            'message' => 'registration successfully',
            'data' => $user,
        ], 201);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Logout successfully'
        ], 200);
    }
}
