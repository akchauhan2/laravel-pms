<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BugTicketController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

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

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Project Routes
    Route::apiResource('projects', ProjectController::class);

    // Task Routes
    Route::apiResource('tasks', TaskController::class);

    // BugTicket Routes
    Route::apiResource('bugs', BugTicketController::class);

    // User Routes
    Route::post('users', [UserController::class, 'store']);

    // Discussion Routes
    Route::apiResource('discussions', DiscussionController::class);
    Route::get('projects/{project}/discussions', [DiscussionController::class, 'getDiscussionsByProject']);
});
