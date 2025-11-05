<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Wallet, WalletGroup};

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::with('walletGroup')
            ->whereHas('walletGroup') // Ensure walletGroup exists
            ->orderBy('name')
            ->paginate(20);
        return view('wallets.index', compact('wallets'));
    }

    public function create()
    {
        $groups = WalletGroup::orderBy('name')->get();
        return view('wallets.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_group_id' => 'required|exists:wallet_groups,id',
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = (bool)($validated['is_active'] ?? true);

        Wallet::create($validated);

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil dibuat');
    }

    public function edit(Wallet $wallet)
    {
        $groups = WalletGroup::orderBy('name')->get();
        return view('wallets.edit', compact('wallet', 'groups'));
    }

    public function update(Request $request, Wallet $wallet)
    {
        $validated = $request->validate([
            'wallet_group_id' => 'required|exists:wallet_groups,id',
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = (bool)($validated['is_active'] ?? $wallet->is_active);

        $wallet->update($validated);

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil diperbarui');
    }

    public function destroy(Wallet $wallet)
    {
        $wallet->delete();
        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil dihapus');
    }
}


