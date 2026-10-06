<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Illuminate\Http\Request $request) {
    return redirect()->route($request->user()->getDashboardRoute());
})->middleware(['auth', 'active_user', 'verified'])->name('dashboard');

Route::middleware(['auth', 'active_user', 'verified'])->group(function () {
    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
    });

    // Reseller Routes
    Route::middleware(['reseller'])->prefix('reseller')->name('reseller.')->group(function () {
        Route::get('/dashboard', function () { return view('reseller.dashboard'); })->name('dashboard');
    });

    // Customer Routes
    Route::middleware(['customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () { return view('customer.dashboard'); })->name('dashboard');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
