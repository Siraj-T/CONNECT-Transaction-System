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
        
        // Voucher Plans
        Route::get('/voucher-plans', [\App\Http\Controllers\Admin\VoucherPlanController::class, 'index'])->name('voucher-plans.index');
        Route::get('/voucher-plans/create', [\App\Http\Controllers\Admin\VoucherPlanController::class, 'create'])->name('voucher-plans.create');
        Route::post('/voucher-plans', [\App\Http\Controllers\Admin\VoucherPlanController::class, 'store'])->name('voucher-plans.store');
        Route::get('/voucher-plans/{voucherPlan}/edit', [\App\Http\Controllers\Admin\VoucherPlanController::class, 'edit'])->name('voucher-plans.edit');
        Route::put('/voucher-plans/{voucherPlan}', [\App\Http\Controllers\Admin\VoucherPlanController::class, 'update'])->name('voucher-plans.update');
        Route::delete('/voucher-plans/{voucherPlan}', [\App\Http\Controllers\Admin\VoucherPlanController::class, 'destroy'])->name('voucher-plans.destroy');

        // Users
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        // Vouchers Generate
        Route::get('/vouchers/generate', [\App\Http\Controllers\Admin\VoucherGeneratorController::class, 'create'])->name('vouchers.generate');
        Route::post('/vouchers/generate', [\App\Http\Controllers\Admin\VoucherGeneratorController::class, 'store'])->name('vouchers.generate.store');
    });

    // Reseller Routes
    Route::middleware(['reseller'])->prefix('reseller')->name('reseller.')->group(function () {
        Route::get('/dashboard', function () { return view('reseller.dashboard'); })->name('dashboard');
        
        // Buy Vouchers
        Route::get('/vouchers/buy', [\App\Http\Controllers\Reseller\ResellerVoucherController::class, 'buyIndex'])->name('vouchers.buy');
        Route::post('/vouchers/buy', [\App\Http\Controllers\Reseller\ResellerVoucherController::class, 'buyStore'])->name('vouchers.buy.store');
    });

    // Customer Routes
    Route::middleware(['customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () { return view('customer.dashboard'); })->name('dashboard');
        
        // Redeem Voucher
        Route::get('/vouchers/redeem', [\App\Http\Controllers\Customer\CustomerVoucherController::class, 'redeemIndex'])->name('vouchers.redeem');
        Route::post('/vouchers/redeem', [\App\Http\Controllers\Customer\CustomerVoucherController::class, 'redeemStore'])->name('vouchers.redeem.store');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
