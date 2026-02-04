<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ExternalStatisticController;
use App\Http\Controllers\Api\V1\StatisticController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // External API routes (API key authentication via HMAC signature)
    Route::middleware('api.key')->group(function () {
        Route::post('/external/statistics', [ExternalStatisticController::class, 'store']);
    });

    // Protected routes (require user authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Statistics CRUD (for admin panel)
        Route::apiResource('statistics', StatisticController::class);
    });
});
