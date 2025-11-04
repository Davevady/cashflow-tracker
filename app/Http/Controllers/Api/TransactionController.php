<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource with category and group relations.
     */
    public function index(): JsonResponse
    {
        $transactions = Transaction::with([
            'category.transactionGroup'
        ])
        ->orderBy('date', 'desc')
        ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $transaction = Transaction::create($validated);
        $transaction->load('category.transactionGroup');

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditambahkan',
            'data' => $transaction,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load('category.transactionGroup');

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'amount' => 'sometimes|numeric|min:0',
            'date' => 'sometimes|date',
            'note' => 'nullable|string',
        ]);

        $transaction->update($validated);
        $transaction->load('category.transactionGroup');

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diupdate',
            'data' => $transaction,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }
}
