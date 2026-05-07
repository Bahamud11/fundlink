<?php

use App\Http\Controllers\Api\FundlinkApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [FundlinkApiController::class, 'login']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [FundlinkApiController::class, 'logout']);
    Route::get('/dashboard', [FundlinkApiController::class, 'dashboard']);
    Route::get('/transactions', [FundlinkApiController::class, 'transactions']);
    Route::post('/transactions', [FundlinkApiController::class, 'storeTransaction']);
    Route::get('/user', [FundlinkApiController::class, 'user']);
    Route::get('/notifications', [FundlinkApiController::class, 'notifications']);
});
