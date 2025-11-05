<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionGroupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WalletGroupController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WalletTransferController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TrashController;
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

        // Role Management
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

        // Permission Management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{role}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{role}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::post('/permissions/create', [PermissionController::class, 'createPermission'])->name('permissions.create');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroyPermission'])->name('permissions.destroy');
    });

    // Transaction Group Management (Admin & Manager)
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::resource('transaction-groups', TransactionGroupController::class);
    });

    // Category Management (Admin & Manager)
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    // Wallet Management (Admin & Manager)
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::resource('wallet-groups', WalletGroupController::class)->except(['show']);
        Route::resource('wallets', WalletController::class)->except(['show']);
        Route::resource('wallet-transfers', WalletTransferController::class)->except(['show']);
        Route::resource('members', MemberController::class)->except(['show']);
    });

    // Trash Management (Admin & Manager)
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
        Route::post('/trash/restore', [TrashController::class, 'restore'])->name('trash.restore');
        Route::delete('/trash/force-delete', [TrashController::class, 'forceDelete'])->name('trash.forceDelete');
        Route::delete('/trash/empty', [TrashController::class, 'emptyTrash'])->name('trash.empty');
    });
});
