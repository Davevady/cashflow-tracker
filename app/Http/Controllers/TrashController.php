<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Transaction,
    Category,
    TransactionGroup,
    Wallet,
    WalletGroup,
    WalletTransfer,
    Member
};

class TrashController extends Controller
{
    /**
     * Display all soft deleted data grouped by model
     */
    public function index()
    {
        // Get all soft deleted data
        $deletedTransactions = Transaction::onlyTrashed()
            ->with(['category' => function($query) {
                $query->withTrashed();
            }, 'wallet' => function($query) {
                $query->withTrashed();
            }, 'member' => function($query) {
                $query->withTrashed();
            }])
            ->orderBy('deleted_at', 'desc')
            ->get();

        $deletedCategories = Category::onlyTrashed()
            ->with(['transactionGroup' => function($query) {
                $query->withTrashed();
            }])
            ->withCount('transactions')
            ->orderBy('deleted_at', 'desc')
            ->get();

        $deletedTransactionGroups = TransactionGroup::onlyTrashed()
            ->withCount('categories')
            ->orderBy('deleted_at', 'desc')
            ->get();

        $deletedWallets = Wallet::onlyTrashed()
            ->with(['walletGroup' => function($query) {
                $query->withTrashed();
            }])
            ->orderBy('deleted_at', 'desc')
            ->get();

        $deletedWalletGroups = WalletGroup::onlyTrashed()
            ->withCount('wallets')
            ->orderBy('deleted_at', 'desc')
            ->get();

        $deletedWalletTransfers = WalletTransfer::onlyTrashed()
            ->with(['fromWallet' => function($query) {
                $query->withTrashed();
            }, 'toWallet' => function($query) {
                $query->withTrashed();
            }])
            ->orderBy('deleted_at', 'desc')
            ->get();

        $deletedMembers = Member::onlyTrashed()
            ->withCount('transactions')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('trash.index', compact(
            'deletedTransactions',
            'deletedCategories',
            'deletedTransactionGroups',
            'deletedWallets',
            'deletedWalletGroups',
            'deletedWalletTransfers',
            'deletedMembers'
        ));
    }

    /**
     * Restore a specific item
     */
    public function restore(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        $model = $this->getModel($type);

        if (!$model) {
            return redirect()->back()->with('error', 'Tipe data tidak valid');
        }

        $item = $model::onlyTrashed()->find($id);

        if (!$item) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $item->restore();

        return redirect()->back()->with('success', 'Data berhasil dipulihkan');
    }

    /**
     * Permanently delete a specific item
     */
    public function forceDelete(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        $model = $this->getModel($type);

        if (!$model) {
            return redirect()->back()->with('error', 'Tipe data tidak valid');
        }

        $item = $model::onlyTrashed()->find($id);

        if (!$item) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $item->forceDelete();

        return redirect()->back()->with('success', 'Data berhasil dihapus permanen');
    }

    /**
     * Empty all trash (permanently delete all soft deleted items)
     */
    public function emptyTrash()
    {
        // Delete in order to avoid foreign key constraints
        Transaction::onlyTrashed()->forceDelete();
        WalletTransfer::onlyTrashed()->forceDelete();
        Category::onlyTrashed()->forceDelete();
        Wallet::onlyTrashed()->forceDelete();
        Member::onlyTrashed()->forceDelete();
        TransactionGroup::onlyTrashed()->forceDelete();
        WalletGroup::onlyTrashed()->forceDelete();

        return redirect()->back()->with('success', 'Semua data sampah berhasil dikosongkan');
    }

    /**
     * Get model class from type string
     */
    private function getModel($type)
    {
        return match($type) {
            'transaction' => Transaction::class,
            'category' => Category::class,
            'transaction_group' => TransactionGroup::class,
            'wallet' => Wallet::class,
            'wallet_group' => WalletGroup::class,
            'wallet_transfer' => WalletTransfer::class,
            'member' => Member::class,
            default => null,
        };
    }
}
