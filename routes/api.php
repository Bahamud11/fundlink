<?php

use App\Http\Controllers\Api\FundlinkApiController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::post('/login',    [FundlinkApiController::class, 'login']);
Route::post('/register', [FundlinkApiController::class, 'register']);
Route::get('/categories', [FundlinkApiController::class, 'categories']);

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // Auth
    Route::post('/logout',     [FundlinkApiController::class, 'logout']);
    Route::post('/logout-all', [FundlinkApiController::class, 'logoutAll']);

    // User
    Route::get('/user',                [FundlinkApiController::class, 'user']);
    Route::post('/user/profile',       [FundlinkApiController::class, 'updateProfile']);
    Route::post('/user/password',      [FundlinkApiController::class, 'changePassword']);

    // Dashboard
    Route::get('/dashboard', [FundlinkApiController::class, 'dashboard']);

    // Transactions
    Route::get('/transactions',          [FundlinkApiController::class, 'transactions']);
    Route::post('/transactions',         [FundlinkApiController::class, 'storeTransaction']);
    Route::get('/transactions/{id}',     [FundlinkApiController::class, 'showTransaction']);
    Route::post('/transactions/{id}',    [FundlinkApiController::class, 'updateTransaction']);  // POST karena multipart
    Route::delete('/transactions/{id}',  [FundlinkApiController::class, 'deleteTransaction']);

    // Notifications
    Route::get('/notifications',              [FundlinkApiController::class, 'notifications']);
    Route::post('/notifications/read-all',    [FundlinkApiController::class, 'markAllNotificationsRead']);
    Route::post('/notifications/{id}/read',   [FundlinkApiController::class, 'markNotificationRead']);

    // Units
    Route::get('/units',          [FundlinkApiController::class, 'units']);
    Route::get('/units/{id}',     [FundlinkApiController::class, 'showUnit']);
    Route::post('/units',         [FundlinkApiController::class, 'storeUnit']);
    Route::put('/units/{id}',     [FundlinkApiController::class, 'updateUnit']);
    Route::delete('/units/{id}',  [FundlinkApiController::class, 'deleteUnit']);

    // Users (Admin)
    Route::get('/users',          [FundlinkApiController::class, 'users']);
    Route::post('/users',         [FundlinkApiController::class, 'storeUser']);
    Route::put('/users/{id}',     [FundlinkApiController::class, 'updateUser']);
    Route::delete('/users/{id}',  [FundlinkApiController::class, 'deleteUser']);
});
