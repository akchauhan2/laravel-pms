<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json([
                'successFlag' => true,
                'message' => 'Login Successful',
                'token' => $user->createToken('API Token')->plainTextToken,
            ]);
        }

        return response()->json([
            'successFlag' => false,
            'message' => 'Invalid Credentials',
        ], 401);
    }
}
