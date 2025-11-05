<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletTransferController extends Controller
{
    public function index()
    {
        $transfers = WalletTransfer::with(['fromWallet', 'toWallet'])
            ->whereHas('fromWallet') // Ensure fromWallet exists
            ->whereHas('toWallet') // Ensure toWallet exists
            ->orderByDesc('transfer_date')
            ->paginate(20);
        return view('wallet-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $wallets = Wallet::orderBy('name')->get();
        return view('wallet-transfers.create', compact('wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_wallet_id' => 'required|different:to_wallet_id|exists:wallets,id',
            'to_wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0.01',
            'fee' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:1000',
            'transfer_date' => 'required|date',
        ]);

        $validated['fee'] = $validated['fee'] ?? 0;

        DB::transaction(function () use ($validated) {
            // Create transfer record
            $transfer = WalletTransfer::create($validated);

            // Adjust balances
            $from = Wallet::lockForUpdate()->find($validated['from_wallet_id']);
            $to = Wallet::lockForUpdate()->find($validated['to_wallet_id']);

            $from->balance = $from->balance - $validated['amount'] - $validated['fee'];
            $to->balance = $to->balance + $validated['amount'];

            $from->save();
            $to->save();

            return $transfer;
        });

        return redirect()->route('wallet-transfers.index')->with('success', 'Transfer berhasil dibuat');
    }

    public function edit(WalletTransfer $wallet_transfer)
    {
        $wallets = Wallet::orderBy('name')->get();
        return view('wallet-transfers.edit', ['transfer' => $wallet_transfer, 'wallets' => $wallets]);
    }

    public function update(Request $request, WalletTransfer $wallet_transfer)
    {
        $validated = $request->validate([
            'from_wallet_id' => 'required|different:to_wallet_id|exists:wallets,id',
            'to_wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0.01',
            'fee' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:1000',
            'transfer_date' => 'required|date',
        ]);

        $validated['fee'] = $validated['fee'] ?? 0;

        DB::transaction(function () use ($validated, $wallet_transfer) {
            // Reverse previous balances
            $oldFrom = Wallet::lockForUpdate()->find($wallet_transfer->from_wallet_id);
            $oldTo = Wallet::lockForUpdate()->find($wallet_transfer->to_wallet_id);

            $oldFrom->balance = $oldFrom->balance + $wallet_transfer->amount + $wallet_transfer->fee;
            $oldTo->balance = $oldTo->balance - $wallet_transfer->amount;
            $oldFrom->save();
            $oldTo->save();

            // Update transfer
            $wallet_transfer->update($validated);

            // Apply new balances
            $newFrom = Wallet::lockForUpdate()->find($validated['from_wallet_id']);
            $newTo = Wallet::lockForUpdate()->find($validated['to_wallet_id']);

            $newFrom->balance = $newFrom->balance - $validated['amount'] - $validated['fee'];
            $newTo->balance = $newTo->balance + $validated['amount'];
            $newFrom->save();
            $newTo->save();
        });

        return redirect()->route('wallet-transfers.index')->with('success', 'Transfer berhasil diperbarui');
    }

    public function destroy(WalletTransfer $wallet_transfer)
    {
        DB::transaction(function () use ($wallet_transfer) {
            // Reverse balances
            $from = Wallet::lockForUpdate()->find($wallet_transfer->from_wallet_id);
            $to = Wallet::lockForUpdate()->find($wallet_transfer->to_wallet_id);

            $from->balance = $from->balance + $wallet_transfer->amount + $wallet_transfer->fee;
            $to->balance = $to->balance - $wallet_transfer->amount;
            $from->save();
            $to->save();

            $wallet_transfer->delete();
        });

        return redirect()->route('wallet-transfers.index')->with('success', 'Transfer berhasil dihapus');
    }
}


