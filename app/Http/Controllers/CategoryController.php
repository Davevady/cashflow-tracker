<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\TransactionGroup;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('transactionGroup')
            ->whereHas('transactionGroup') // Ensure transactionGroup exists
            ->withCount('transactions')
            ->orderBy('name')
            ->paginate(20);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $transactionGroups = TransactionGroup::orderBy('type')->orderBy('name')->get();
        return view('categories.create', compact('transactionGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:transaction_groups,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ], [
            'group_id.required' => 'Grup transaksi harus dipilih',
            'group_id.exists' => 'Grup transaksi tidak valid',
            'name.required' => 'Nama kategori harus diisi',
            'name.max' => 'Nama kategori maksimal 255 karakter',
            'description.max' => 'Deskripsi maksimal 500 karakter',
        ]);

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['transactionGroup', 'transactions']);

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $transactionGroups = TransactionGroup::orderBy('type')->orderBy('name')->get();
        return view('categories.edit', compact('category', 'transactionGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:transaction_groups,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ], [
            'group_id.required' => 'Grup transaksi harus dipilih',
            'group_id.exists' => 'Grup transaksi tidak valid',
            'name.required' => 'Nama kategori harus diisi',
            'name.max' => 'Nama kategori maksimal 255 karakter',
            'description.max' => 'Deskripsi maksimal 500 karakter',
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has transactions
        if ($category->transactions()->count() > 0) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki transaksi');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
