<?php

use App\Http\Controllers\Api\FundlinkApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [FundlinkApiController::class, 'login']);
Route::post('/register', [FundlinkApiController::class, 'register']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [FundlinkApiController::class, 'logout']);
    Route::get('/dashboard', [FundlinkApiController::class, 'dashboard']);
    Route::get('/transactions', [FundlinkApiController::class, 'transactions']);
    Route::post('/transactions', [FundlinkApiController::class, 'storeTransaction']);
    
    Route::get('/user', [FundlinkApiController::class, 'user']);
    Route::post('/user/profile', [FundlinkApiController::class, 'updateProfile']);
    
    Route::get('/notifications', [FundlinkApiController::class, 'notifications']);
    Route::post('/notifications/{id}/read', [FundlinkApiController::class, 'markNotificationRead']);
    
    // Admin only
    Route::get('/units', [FundlinkApiController::class, 'units']);
    Route::get('/users', [FundlinkApiController::class, 'users']);
});
