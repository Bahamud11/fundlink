<?php

use App\Http\Controllers\Api\FundlinkApiController;
use Illuminate\Support\Facades\Route;

Route::post('/api/login', [FundlinkApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/api/logout', [FundlinkApiController::class, 'logout']);
    Route::get('/api/dashboard', [FundlinkApiController::class, 'dashboard']);
    Route::get('/api/transactions', [FundlinkApiController::class, 'transactions']);
    Route::post('/api/transactions', [FundlinkApiController::class, 'storeTransaction']);
});
