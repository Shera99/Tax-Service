<?php

use App\Http\Controllers\Web\ApiKeyController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

// Редирект на логин
Route::get('/', function () {
    return redirect()->route('login');
});

// Гостевые маршруты
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Аутентифицированные маршруты
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard - доступен всем авторизованным
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');

    // Управление пользователями и API ключами - только для админа
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);

        // API Keys management
        Route::resource('api-keys', ApiKeyController::class)->except(['edit', 'update']);
        Route::post('api-keys/{apiKey}/toggle', [ApiKeyController::class, 'toggle'])->name('api-keys.toggle');
    });
});
