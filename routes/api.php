<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

// Public Authentication Route (Sanctum Login)
Route::post('/login', [AuthController::class, 'apiLogin'])->name('api.login');

// Protected Routes (Sanctum Bearer Token Required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/transactions', [TransactionController::class, 'store'])->name('api.transactions.store');
    Route::post('/logout', [AuthController::class, 'apiLogout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'apiMe'])->name('api.me');
});
