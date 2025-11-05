<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Transaction, User, Category, Wallet, Member};

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Get recent transactions
        $recentTransactions = Transaction::with(['category.transactionGroup', 'wallet', 'member'])
            ->whereHas('category.transactionGroup') // Ensure category and transactionGroup exist
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Calculate summary using joins - convert to integer to remove .00
        $totalIncome = (int) Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->join('transaction_groups', 'categories.group_id', '=', 'transaction_groups.id')
            ->whereNull('categories.deleted_at')
            ->whereNull('transaction_groups.deleted_at')
            ->where('transaction_groups.type', 'in')
            ->sum('transactions.amount');

        $totalExpense = (int) Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->join('transaction_groups', 'categories.group_id', '=', 'transaction_groups.id')
            ->whereNull('categories.deleted_at')
            ->whereNull('transaction_groups.deleted_at')
            ->where('transaction_groups.type', 'out')
            ->sum('transactions.amount');

        $balance = $totalIncome - $totalExpense;

        // Get all users if admin
        $users = null;
        if ($user->hasRole('admin')) {
            $users = User::with('roles')->paginate(10);
        }

        // Get categories, wallets, members for transaction form
        $categories = Category::with('transactionGroup')
            ->orderBy('name')
            ->get();
        $wallets = Wallet::orderBy('name')->get();
        $members = Member::orderBy('name')->get();

        // Aggregations for charts
        $byCategory = Transaction::selectRaw('categories.name as label, SUM(transactions.amount) as total, transaction_groups.type as type')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->join('transaction_groups', 'categories.group_id', '=', 'transaction_groups.id')
            ->whereNull('categories.deleted_at')
            ->whereNull('transaction_groups.deleted_at')
            ->groupBy('categories.name', 'transaction_groups.type')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $byMember = Transaction::selectRaw('COALESCE(members.name, "Tidak Ditentukan") as label, SUM(transactions.amount) as total, transaction_groups.type as type')
            ->leftJoin('members', 'transactions.member_id', '=', 'members.id')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->join('transaction_groups', 'categories.group_id', '=', 'transaction_groups.id')
            ->whereNull('categories.deleted_at')
            ->whereNull('transaction_groups.deleted_at')
            ->where(function($query) {
                $query->whereNull('members.deleted_at')
                      ->orWhereNull('transactions.member_id');
            })
            ->groupBy('label', 'type')
            ->orderByDesc('total')
            ->get();

        $byWallet = Transaction::selectRaw('wallets.name as label, SUM(transactions.amount) as total, transaction_groups.type as type')
            ->join('wallets', 'transactions.wallet_id', '=', 'wallets.id')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->join('transaction_groups', 'categories.group_id', '=', 'transaction_groups.id')
            ->whereNull('wallets.deleted_at')
            ->whereNull('categories.deleted_at')
            ->whereNull('transaction_groups.deleted_at')
            ->groupBy('label', 'type')
            ->orderByDesc('total')
            ->get();

        $monthly = Transaction::selectRaw('DATE_FORMAT(transactions.date, "%Y-%m") as ym, SUM(CASE WHEN transaction_groups.type = "in" THEN transactions.amount ELSE 0 END) as income, SUM(CASE WHEN transaction_groups.type = "out" THEN transactions.amount ELSE 0 END) as expense')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->join('transaction_groups', 'categories.group_id', '=', 'transaction_groups.id')
            ->whereNull('categories.deleted_at')
            ->whereNull('transaction_groups.deleted_at')
            ->groupBy('ym')
            ->orderBy('ym')
            ->limit(12)
            ->get();

        return view('dashboard', compact(
            'user',
            'recentTransactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'users',
            'categories',
            'wallets',
            'members',
            'byCategory',
            'byMember',
            'byWallet',
            'monthly'
        ));
    }
}
