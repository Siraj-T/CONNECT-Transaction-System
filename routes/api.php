<?php

use App\Http\Controllers\Citizen\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/transactions', [TransactionController::class, 'store']);
