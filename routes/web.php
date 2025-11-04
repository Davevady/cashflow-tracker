<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Welcome Page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaction Management
    Route::resource('transactions', TransactionController::class);

    // User Management (Admin Only)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
