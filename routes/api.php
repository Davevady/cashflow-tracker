<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{TransactionController, WalletSyncController};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Transaction API Routes
Route::apiResource('transactions', TransactionController::class)->names([
    'index' => 'api.transactions.index',
    'store' => 'api.transactions.store',
    'show' => 'api.transactions.show',
    'update' => 'api.transactions.update',
    'destroy' => 'api.transactions.destroy',
]);

// Wallet Sync API Routes (for Money+ scraper)
Route::post('/wallets/sync-from-scraper', [WalletSyncController::class, 'syncFromScraper']);
Route::get('/wallets/sync-status', [WalletSyncController::class, 'status']);
