<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('transactions', \App\Livewire\TransactionManager::class)->name('transactions');
    Route::get('notifications', \App\Livewire\NotificationManager::class)->name('notifications');

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('units', \App\Livewire\UnitManager::class)->name('units');
        Route::get('users', \App\Livewire\UserManager::class)->name('users');
    });
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
