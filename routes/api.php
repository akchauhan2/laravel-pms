<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BugTicketController;
use App\Http\Controllers\UserController;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        return response()->json([
            'token' => $user->createToken('MyApp')->plainTextToken,
        ]);
    }

    return response()->json(['message' => 'Unauthorized'], 401);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Project Routes
Route::apiResource('projects', ProjectController::class);

// Task Routes
Route::apiResource('tasks', TaskController::class);

// BugTicket Routes
Route::apiResource('bugs', BugTicketController::class);

// routes/api.php


Route::post('users', [UserController::class, 'store']); // POST request to create a new user
