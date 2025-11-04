<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Transaction, User, Category};

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Get recent transactions
        $recentTransactions = Transaction::with('category.transactionGroup')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Calculate summary
        $totalIncome = Transaction::whereHas('category', function($query) {
            $query->where('type', 'income');
        })->sum('amount');

        $totalExpense = Transaction::whereHas('category', function($query) {
            $query->where('type', 'expense');
        })->sum('amount');

        $balance = $totalIncome - $totalExpense;

        // Get all users if admin
        $users = null;
        if ($user->hasRole('admin')) {
            $users = User::with('roles')->paginate(10);
        }

        // Get categories for transaction form
        $categories = Category::with('transactionGroup')->get();

        return view('dashboard', compact(
            'user',
            'recentTransactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'users',
            'categories'
        ));
    }
}
