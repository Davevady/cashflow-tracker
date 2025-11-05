<?php

namespace App\Http\Controllers;

use App\Models\{Transaction, Category, Wallet, Member};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource with category and group relations.
     */
    public function index()
    {
        $transactions = Transaction::with([
            'category.transactionGroup', 'wallet', 'member'
        ])
        ->whereHas('category.transactionGroup') // Ensure category and transactionGroup exist
        ->orderBy('date', 'desc')
        ->paginate(20);

        // Get categories for edit modal
        $categories = Category::with('transactionGroup')
            ->orderBy('name')
            ->get();
        $wallets = Wallet::orderBy('name')->get();
        $members = Member::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories', 'wallets', 'members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'wallet_id' => 'required|exists:wallets,id',
            'member_id' => 'nullable|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create($validated);

            // adjust wallet balance according to transaction type
            $category = $transaction->category()->with('transactionGroup')->first();
            $type = $category->transactionGroup->type; // 'in' or 'out'
            $wallet = Wallet::lockForUpdate()->find($transaction->wallet_id);
            if ($type === 'in') {
                $wallet->balance = $wallet->balance + $transaction->amount;
            } else {
                $wallet->balance = $wallet->balance - $transaction->amount;
            }
            $wallet->save();
        });

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
            'wallet_id' => 'required|exists:wallets,id',
            'member_id' => 'nullable|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $transaction) {
            // reverse old balance
            $oldCategory = $transaction->category()->with('transactionGroup')->first();
            $oldType = $oldCategory->transactionGroup->type;
            $oldWallet = Wallet::lockForUpdate()->find($transaction->wallet_id);
            if ($oldWallet) {
                if ($oldType === 'in') {
                    $oldWallet->balance = $oldWallet->balance - $transaction->amount;
                } else {
                    $oldWallet->balance = $oldWallet->balance + $transaction->amount;
                }
                $oldWallet->save();
            }

            // update transaction
            $transaction->update($validated);

            // apply new balance
            $newCategory = $transaction->category()->with('transactionGroup')->first();
            $newType = $newCategory->transactionGroup->type;
            $newWallet = Wallet::lockForUpdate()->find($transaction->wallet_id);
            if ($newType === 'in') {
                $newWallet->balance = $newWallet->balance + $transaction->amount;
            } else {
                $newWallet->balance = $newWallet->balance - $transaction->amount;
            }
            $newWallet->save();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            // reverse wallet balance impact
            $category = $transaction->category()->with('transactionGroup')->first();
            $type = $category->transactionGroup->type;
            $wallet = Wallet::lockForUpdate()->find($transaction->wallet_id);
            if ($wallet) {
                if ($type === 'in') {
                    $wallet->balance = $wallet->balance - $transaction->amount;
                } else {
                    $wallet->balance = $wallet->balance + $transaction->amount;
                }
                $wallet->save();
            }

            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}
