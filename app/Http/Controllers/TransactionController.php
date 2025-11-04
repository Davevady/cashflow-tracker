<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource with category and group relations.
     */
    public function index()
    {
        $transactions = Transaction::with([
            'category.transactionGroup'
        ])
        ->orderBy('date', 'desc')
        ->paginate(20);

        // Get categories for edit modal
        $categories = Category::with('transactionGroup')->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        Transaction::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}
