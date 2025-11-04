@extends('layouts.app')

@section('title', 'Dashboard - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Dashboard</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="{{ route('dashboard') }}">
                <i class="flaticon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Dashboard</a>
        </li>
    </ul>
</div>

<!-- Summary Cards -->
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body ">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="flaticon-coins"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pemasukan</p>
                            <h4 class="card-title">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="flaticon-shopping-bag"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pengeluaran</p>
                            <h4 class="card-title">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-{{ $balance >= 0 ? 'primary' : 'warning' }} bubble-shadow-small">
                            <i class="flaticon-graph"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Saldo</p>
                            <h4 class="card-title">Rp {{ number_format($balance, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="flaticon-interface-6"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Transaksi</p>
                            <h4 class="card-title">{{ $recentTransactions->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Form Add Transaction -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Tambah Transaksi</div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Transaction Type Toggle -->
                <div class="form-group">
                    <label class="font-weight-bold">Tipe Transaksi</label>
                    <div class="btn-group btn-group-toggle w-100 transaction-type-toggle" data-toggle="buttons">
                        <label class="btn btn-success active" id="btn-income">
                            <input type="radio" name="type_toggle" value="income" checked>
                            <i class="fa fa-arrow-down"></i> Cash In
                        </label>
                        <label class="btn btn-danger" id="btn-expense">
                            <input type="radio" name="type_toggle" value="expense">
                            <i class="fa fa-arrow-up"></i> Cash Out
                        </label>
                    </div>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                    @csrf
                    <div class="form-group">
                        <label for="category_id">Kategori</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories->where('type', 'income')->groupBy('transactionGroup.name') as $groupName => $groupCategories)
                                <optgroup label="{{ $groupName }}" class="category-group income-category">
                                    @foreach($groupCategories as $category)
                                        <option value="{{ $category->id }}" data-type="income" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->icon }} {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                            @foreach($categories->where('type', 'expense')->groupBy('transactionGroup.name') as $groupName => $groupCategories)
                                <optgroup label="{{ $groupName }}" class="category-group expense-category" style="display: none;">
                                    @foreach($groupCategories as $category)
                                        <option value="{{ $category->id }}" data-type="expense" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->icon }} {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Jumlah</label>
                        <input type="text" class="form-control" id="amount" name="amount" placeholder="0" value="{{ old('amount') }}" required>
                        <small class="form-text text-muted">Masukkan nominal tanpa titik atau koma</small>
                    </div>
                    <div class="form-group">
                        <label for="date">Tanggal & Waktu</label>
                        <input type="datetime-local" class="form-control" id="date" name="date" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                        <small class="form-text text-muted">Format: 24 jam (00:00 - 23:59)</small>
                    </div>
                    <div class="form-group">
                        <label for="note">Catatan (Opsional)</label>
                        <textarea class="form-control" id="note" name="note" rows="3" placeholder="Tambahkan catatan...">{{ old('note') }}</textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-save"></i> Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Transaksi Terbaru</h4>
                    <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-list"></i> Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Catatan</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>
                                    <div class="text-dark font-weight-bold">@shortIndonesianDate($transaction->created_at)</div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $transaction->category->type === 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->category->icon }} {{ $transaction->category->name }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($transaction->note ?? '-', 30) }}</td>
                                <td class="text-right">
                                    <span class="text-{{ $transaction->category->type === 'income' ? 'success' : 'danger' }} font-weight-bold">
                                        {{ $transaction->category->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Management (Admin Only) -->
{{-- @role('admin')
@if($users)
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Manajemen User</h4>
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Dibuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $userItem)
                            <tr>
                                <td>{{ $userItem->name }}</td>
                                <td>{{ $userItem->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $userItem->hasRole('admin') ? 'primary' : ($userItem->hasRole('manager') ? 'info' : 'secondary') }}">
                                        {{ $userItem->getRoleNames()->first() ?? 'No Role' }}
                                    </span>
                                </td>
                                <td>@shortIndonesianDate($userItem->created_at)</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('users.edit', $userItem) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if($userItem->id !== $user->id)
                                        <form method="POST" action="{{ route('users.destroy', $userItem) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endrole --}}
@endsection

@push('styles')
<style>
    /* Custom styling for transaction type toggle */
    .transaction-type-toggle {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .transaction-type-toggle .btn {
        width: 50%;
        border-radius: 0; /* opsional: agar bentuk rapi menyatu */
        border: none;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        position: relative;
        opacity: 0.6;
    }

    .transaction-type-toggle .btn:first-child {
        border-top-left-radius: 0.5rem;
        border-bottom-left-radius: 0.5rem;
    }

    .transaction-type-toggle .btn:last-child {
        border-top-right-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    .transaction-type-toggle .btn:not(.active) {
        background-color: #f5f5f5 !important;
        color: #666 !important;
    }

    .transaction-type-toggle .btn.active {
        opacity: 1;
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .transaction-type-toggle .btn-success.active {
        background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%) !important;
        color: white !important;
    }

    .transaction-type-toggle .btn-danger.active {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%) !important;
        color: white !important;
    }

    .transaction-type-toggle .btn i {
        margin-right: 5px;
        font-size: 16px;
    }

    .transaction-type-toggle .btn:hover:not(.active) {
        opacity: 0.8;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Transaction type toggle functionality
    $('input[name="type_toggle"]').on('change', function() {
        const selectedType = $(this).val();
        const categorySelect = $('#category_id');
        const $parentLabel = $(this).parent();

        // Update active state visually
        $('.transaction-type-toggle .btn').removeClass('active');
        $parentLabel.addClass('active');

        // Reset select
        categorySelect.val('');

        // Hide all categories first
        categorySelect.find('option[data-type]').hide();
        categorySelect.find('optgroup').hide();

        // Show categories for selected type
        if (selectedType === 'income') {
            categorySelect.find('option[data-type="income"]').show();
            categorySelect.find('.income-category').show();
        } else {
            categorySelect.find('option[data-type="expense"]').show();
            categorySelect.find('.expense-category').show();
        }
    });

    // Handle label click to trigger change
    $('.transaction-type-toggle .btn').on('click', function() {
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
    });

    // Trigger on page load to show correct categories
    $('input[name="type_toggle"]:checked').trigger('change');
});
</script>
@endpush
