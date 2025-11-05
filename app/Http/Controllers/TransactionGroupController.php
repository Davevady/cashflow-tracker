<?php

namespace App\Http\Controllers;

use App\Models\TransactionGroup;
use Illuminate\Http\Request;

class TransactionGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactionGroups = TransactionGroup::withCount('categories')
            ->orderBy('type', 'asc')
            ->orderBy('name')
            ->paginate(20);

        return view('transaction-groups.index', compact('transactionGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('transaction-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:transaction_groups,name',
            'type' => 'required|in:in,out',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama grup transaksi harus diisi',
            'name.unique' => 'Nama grup transaksi sudah digunakan',
            'name.max' => 'Nama grup transaksi maksimal 255 karakter',
            'type.required' => 'Tipe transaksi harus dipilih',
            'type.in' => 'Tipe transaksi tidak valid',
            'description.max' => 'Deskripsi maksimal 500 karakter',
        ]);

        TransactionGroup::create($validated);

        return redirect()
            ->route('transaction-groups.index')
            ->with('success', 'Grup transaksi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionGroup $transactionGroup)
    {
        $transactionGroup->load('categories');

        return view('transaction-groups.show', compact('transactionGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionGroup $transactionGroup)
    {
        return view('transaction-groups.edit', compact('transactionGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionGroup $transactionGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:transaction_groups,name,' . $transactionGroup->id,
            'type' => 'required|in:in,out',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama grup transaksi harus diisi',
            'name.unique' => 'Nama grup transaksi sudah digunakan',
            'name.max' => 'Nama grup transaksi maksimal 255 karakter',
            'type.required' => 'Tipe transaksi harus dipilih',
            'type.in' => 'Tipe transaksi tidak valid',
            'description.max' => 'Deskripsi maksimal 500 karakter',
        ]);

        $transactionGroup->update($validated);

        return redirect()
            ->route('transaction-groups.index')
            ->with('success', 'Grup transaksi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionGroup $transactionGroup)
    {
        // Check if group has categories
        if ($transactionGroup->categories()->count() > 0) {
            return redirect()
                ->route('transaction-groups.index')
                ->with('error', 'Tidak dapat menghapus grup yang masih memiliki kategori');
        }

        $transactionGroup->delete();

        return redirect()
            ->route('transaction-groups.index')
            ->with('success', 'Grup transaksi berhasil dihapus');
    }
}
