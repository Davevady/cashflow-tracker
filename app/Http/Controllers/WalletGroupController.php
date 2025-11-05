<?php

namespace App\Http\Controllers;

use App\Models\WalletGroup;
use Illuminate\Http\Request;

class WalletGroupController extends Controller
{
    public function index()
    {
        $groups = WalletGroup::orderBy('name')->paginate(20);
        return view('wallet-groups.index', compact('groups'));
    }

    public function create()
    {
        return view('wallet-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        WalletGroup::create($validated);

        return redirect()->route('wallet-groups.index')->with('success', 'Grup dompet berhasil dibuat');
    }

    public function edit(WalletGroup $wallet_group)
    {
        return view('wallet-groups.edit', ['group' => $wallet_group]);
    }

    public function update(Request $request, WalletGroup $wallet_group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $wallet_group->update($validated);

        return redirect()->route('wallet-groups.index')->with('success', 'Grup dompet berhasil diperbarui');
    }

    public function destroy(WalletGroup $wallet_group)
    {
        $wallet_group->delete();
        return redirect()->route('wallet-groups.index')->with('success', 'Grup dompet berhasil dihapus');
    }
}


