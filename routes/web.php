<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// OTP verification routes (auth required, but not yet otp-verified)
Route::middleware('auth')->group(function () {
    Route::get('otp/verify', \App\Livewire\OtpVerification::class)->name('otp.verify');
});

Route::middleware(['auth', 'otp.verified'])->group(function () {
    Route::get('dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('transactions', \App\Livewire\TransactionManager::class)->name('transactions');
    Route::get('notifications', \App\Livewire\NotificationManager::class)->name('notifications');

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('units', \App\Livewire\UnitManager::class)->name('units');
        Route::get('users', \App\Livewire\UserManager::class)->name('users');
    });
});

Route::get('profile', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('dashboard');
    }
    return view('profile');
})->middleware(['auth', 'otp.verified'])->name('profile');

require __DIR__.'/auth.php';
