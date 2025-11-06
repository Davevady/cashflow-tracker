<?php

namespace App\Http\Controllers;

use App\Models\{Transaction, Category, Wallet, Member, UserWallet};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource with category and group relations.
     */
    public function index()
    {
        // Filter transactions by current user
        $transactions = Transaction::with([
            'category.transactionGroup', 'wallet', 'member'
        ])
        ->forUser() // Apply user scope
        ->whereHas('category.transactionGroup') // Ensure category and transactionGroup exist
        ->orderBy('date', 'desc')
        ->paginate(20);

        // Get categories for edit modal
        $categories = Category::with('transactionGroup')->orderBy('name')->get();
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

        // Add user_id to validated data
        $validated['user_id'] = auth()->id();

        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create($validated);

            // adjust user wallet balance according to transaction type
            $category = $transaction->category()->with('transactionGroup')->first();
            $type = $category->transactionGroup->type; // 'in' or 'out'

            $userWallet = UserWallet::getOrCreate($validated['user_id'], $transaction->wallet_id);
            $userWallet->updateBalance($transaction->amount, $type);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Check if user owns this transaction
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'wallet_id' => 'required|exists:wallets,id',
            'member_id' => 'nullable|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $transaction) {
            // reverse old balance in user_wallets
            $oldCategory = $transaction->category()->with('transactionGroup')->first();
            $oldType = $oldCategory->transactionGroup->type;
            $oldUserWallet = UserWallet::where('user_id', $transaction->user_id)
                ->where('wallet_id', $transaction->wallet_id)
                ->lockForUpdate()
                ->first();

            if ($oldUserWallet) {
                // Reverse the old transaction
                if ($oldType === 'in') {
                    $oldUserWallet->decrement('balance', $transaction->amount);
                } else {
                    $oldUserWallet->increment('balance', $transaction->amount);
                }
            }

            // update transaction
            $transaction->update($validated);

            // apply new balance in user_wallets
            $newCategory = $transaction->category()->with('transactionGroup')->first();
            $newType = $newCategory->transactionGroup->type;
            $newUserWallet = UserWallet::getOrCreate($transaction->user_id, $transaction->wallet_id);

            if ($newType === 'in') {
                $newUserWallet->increment('balance', $transaction->amount);
            } else {
                $newUserWallet->decrement('balance', $transaction->amount);
            }
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        // Check if user owns this transaction
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        DB::transaction(function () use ($transaction) {
            // reverse user wallet balance impact
            $category = $transaction->category()->with('transactionGroup')->first();
            $type = $category->transactionGroup->type;
            $userWallet = UserWallet::where('user_id', $transaction->user_id)
                ->where('wallet_id', $transaction->wallet_id)
                ->lockForUpdate()
                ->first();

            if ($userWallet) {
                if ($type === 'in') {
                    $userWallet->decrement('balance', $transaction->amount);
                } else {
                    $userWallet->increment('balance', $transaction->amount);
                }
            }

            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}
