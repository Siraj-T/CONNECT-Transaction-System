<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'active_user', 'verified'])->group(function () {
    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/live', [\App\Http\Controllers\Admin\DashboardController::class, 'liveDashboard'])->name('dashboard.live');
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\DashboardController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/audit-logs/live', [\App\Http\Controllers\Admin\DashboardController::class, 'liveAuditLogs'])->name('audit-logs.live');
        Route::put('/transactions/{transaction}/status', [\App\Http\Controllers\Admin\DashboardController::class, 'updateStatus'])->name('transactions.status.update');
    });
});

require __DIR__.'/auth.php';
