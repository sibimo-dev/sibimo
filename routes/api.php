<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

});

Route::middleware([
    'auth:sanctum',
    'permission:user-management',
])
    ->prefix('users')
    ->group(function () {

        Route::get('/', [UserController::class, 'index']);

        Route::post('/', [UserController::class, 'store']);

        Route::get('/{user:user_id}', [UserController::class, 'show']);

        Route::put('/{user:user_id}', [UserController::class, 'update']);

        Route::delete('/{user:user_id}', [UserController::class, 'destroy']);

    });

Route::middleware(['auth:sanctum', 'permission:Dashboard'])
    ->get('/test-permission', function () {
        return response()->json([
            'success' => true,
            'message' => 'Anda memiliki permission Dashboard.',
        ]);
    });