<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    // ...existing code...

    public function someApiMethod(Request $request)
    {
        // Debug: Log the Authorization header
        \Log::info('Authorization Header: ' . $request->header('Authorization'));

        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Debug: Log the user object
        \Log::info('User Object: ' . json_encode($user));

        // ...existing code...
    }

    public function makeApiRequest()
    {
        $token = 'your_token_here'; // Replace with your actual token

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            // ...existing headers...
        ])->get('https://api.example.com/endpoint'); // Replace with your actual endpoint

        if ($response->failed()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ...existing code...
    }
}
