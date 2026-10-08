<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function (Illuminate\Http\Request $request) {
    return redirect()->route($request->user()->getDashboardRoute());
})->middleware(['auth', 'active_user', 'verified'])->name('dashboard');

Route::middleware(['auth', 'active_user', 'verified'])->group(function () {
    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::put('/transactions/{transaction}/status', [\App\Http\Controllers\Admin\DashboardController::class, 'updateStatus'])->name('transactions.status.update');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
