<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BugTicketController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/save-token', [AuthController::class, 'saveToken'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = User::where('api_token', $request->bearerToken())->first();

        return $user;
    });
    Route::post('user-info', [UserController::class, 'getUserInfoByToken']);

    // Project Routes
    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/search', [ProjectController::class, 'search']); // Change to POST

    // Task Routes
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::get('projects/{projectId}/tasks', [TaskController::class, 'getTasksByProject']); // Add this line

    // BugTicket Routes
    Route::apiResource('bugs', BugTicketController::class);

    // User Routes
    Route::post('users', [UserController::class, 'store']);
    Route::get('users', [UserController::class, 'index']);
    Route::post('users/{id}/avatar', [UserController::class, 'updateAvatar']); // Add this line
    Route::put('users/{id}', [UserController::class, 'update']); // Add this line

    // Discussion Routes
    Route::apiResource('discussions', DiscussionController::class);
    Route::get('projects/{project}/discussions', [DiscussionController::class, 'getDiscussionsByProject']);

    // Dashboard Route
    Route::get('dashboard', [DashboardController::class, 'getDashboardData']);
});

Route::middleware('auth:api')->get('/some-endpoint', [ApiController::class, 'someApiMethod']);
