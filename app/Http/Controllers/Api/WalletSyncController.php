<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletSyncController extends Controller
{
    /**
     * Sync wallets from Money+ scraper
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncFromScraper(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wallets' => 'required|array',
            'wallets.*.name' => 'required|string',
            'wallets.*.balance' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $wallets = $request->input('wallets');
            $stats = [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
            ];

            // Get or create default wallet group for Money+
            $walletGroup = WalletGroup::firstOrCreate(
                ['name' => 'Money+ Wallets'],
                [
                    'description' => 'Auto-synced from Money+ app',
                    'icon' => 'fa fa-wallet',
                ]
            );

            foreach ($wallets as $walletData) {
                $name = trim($walletData['name']);
                $balance = (int) $walletData['balance'];

                // Cari wallet berdasarkan nama (case-insensitive)
                $existingWallet = Wallet::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

                if ($existingWallet) {
                    // Update jika balance berubah
                    if ($existingWallet->balance !== $balance) {
                        $existingWallet->update(['balance' => $balance]);
                        $stats['updated']++;

                        Log::info("Wallet updated from Money+ scraper", [
                            'name' => $name,
                            'old_balance' => $existingWallet->balance,
                            'new_balance' => $balance,
                        ]);
                    } else {
                        $stats['skipped']++;
                    }
                } else {
                    // Buat wallet baru
                    Wallet::create([
                        'wallet_group_id' => $walletGroup->id,
                        'name' => $name,
                        'balance' => $balance,
                        'account_number' => null,
                        'icon' => 'fa fa-wallet',
                        'description' => 'Auto-synced from Money+ app',
                        'is_active' => true,
                    ]);

                    $stats['created']++;

                    Log::info("Wallet created from Money+ scraper", [
                        'name' => $name,
                        'balance' => $balance,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Wallets synced successfully',
                'created' => $stats['created'],
                'updated' => $stats['updated'],
                'skipped' => $stats['skipped'],
                'total' => count($wallets),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error syncing wallets from Money+ scraper", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync wallets',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get wallet sync status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        $moneyPlusGroup = WalletGroup::where('name', 'Money+ Wallets')->first();

        if (!$moneyPlusGroup) {
            return response()->json([
                'success' => true,
                'synced' => false,
                'wallet_count' => 0,
                'total_balance' => 0,
            ]);
        }

        $wallets = Wallet::where('wallet_group_id', $moneyPlusGroup->id)->get();
        $totalBalance = $wallets->sum('balance');

        return response()->json([
            'success' => true,
            'synced' => true,
            'wallet_count' => $wallets->count(),
            'total_balance' => $totalBalance,
            'wallets' => $wallets->map(function ($wallet) {
                return [
                    'id' => $wallet->id,
                    'name' => $wallet->name,
                    'balance' => $wallet->balance,
                    'is_active' => $wallet->is_active,
                ];
            }),
        ]);
    }
}
