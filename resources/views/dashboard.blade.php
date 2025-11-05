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
                            <h4 class="card-title"><span class="money-text" data-amount="{{ $totalIncome }}"></span></h4>
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
                            <h4 class="card-title"><span class="money-text" data-amount="{{ $totalExpense }}"></span></h4>
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
                            <h4 class="card-title"><span class="money-text" data-amount="{{ $balance }}"></span></h4>
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
                            <input type="radio" name="type_toggle" value="in" checked>
                            <i class="fa fa-arrow-down"></i> Cash In
                        </label>
                        <label class="btn btn-danger" id="btn-expense">
                            <input type="radio" name="type_toggle" value="out">
                            <i class="fa fa-arrow-up"></i> Cash Out
                        </label>
                    </div>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                    @csrf
                    <input type="hidden" name="type" id="transaction_type" value="in">

                    <div class="form-group">
                        <label for="category_id">Kategori</label>
                        <div class="input-group">
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories->filter(fn($c) => $c->transactionGroup->type === 'in')->groupBy('transactionGroup.name') as $groupName => $groupCategories)
                                    <optgroup label="{{ $groupName }}" class="category-group in-category">
                                        @foreach($groupCategories as $category)
                                            <option value="{{ $category->id }}" data-type="in" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            @include('components.icon-render', ['icon' => $category->icon]) {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                @foreach($categories->filter(fn($c) => $c->transactionGroup->type === 'out')->groupBy('transactionGroup.name') as $groupName => $groupCategories)
                                    <optgroup label="{{ $groupName }}" class="category-group out-category" style="display: none;">
                                        @foreach($groupCategories as $category)
                                            <option value="{{ $category->id }}" data-type="out" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->icon }} {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="wallet_id">Dompet</label>
                        <select class="form-control" id="wallet_id" name="wallet_id" required>
                            <option value="">Pilih Dompet</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}" {{ old('wallet_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->walletGroup->name }} - {{ $w->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="member_id">Anggota</label>
                        <select class="form-control" id="member_id" name="member_id">
                            <option value="">Pilih Anggota (opsional)</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->icon }} {{ $m->name }}
                                </option>
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
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="text-right">
                                    <span class="text-{{ $transaction->category->transactionGroup->type === 'in' ? 'success' : 'danger' }} font-weight-bold">
                                        {{ $transaction->category->transactionGroup->type === 'in' ? '+' : '-' }} <span class="money-text" data-amount="{{ $transaction->amount }}"></span>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark">@shortIndonesianDate($transaction->created_at)</div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $transaction->category->transactionGroup->type === 'in' ? 'success' : 'danger' }}">
                                        {{ $transaction->category->icon }} {{ $transaction->category->name }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($transaction->note ?? '-', 30) }}</td>
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

<!-- Charts Row -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title">Ringkasan per Kategori</div>
                <div>
                    <select id="categoryTypeSelect" class="form-control form-control-sm" style="width:auto;">
                        <option value="out" selected>Pengeluaran</option>
                        <option value="in">Pemasukan</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="chartCategory" height="180"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><div class="card-title">Pemasukan vs Pengeluaran per Bulan</div></div>
            <div class="card-body">
                <canvas id="chartMonthly" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><div class="card-title">Ringkasan per Anggota</div></div>
            <div class="card-body">
                <canvas id="chartMember" height="180"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><div class="card-title">Ringkasan per Dompet</div></div>
            <div class="card-body">
                <canvas id="chartWallet" height="180"></canvas>
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

        // Update hidden type field
        $('#transaction_type').val(selectedType);

        // Reset select
        categorySelect.val('');

        // Hide all categories first
        categorySelect.find('option[data-type]').hide();
        categorySelect.find('optgroup').hide();

        // Show categories for selected type
        if (selectedType === 'in') {
            categorySelect.find('option[data-type="in"]').show();
            categorySelect.find('.in-category').show();
        } else {
            categorySelect.find('option[data-type="out"]').show();
            categorySelect.find('.out-category').show();
        }
    });

    // Handle label click to trigger change
    $('.transaction-type-toggle .btn').on('click', function() {
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
    });

    // Trigger on page load to show correct categories
    $('input[name="type_toggle"]:checked').trigger('change');

    // Charts
    const catRaw = @json($byCategory);

    const monthlyLabels = @json($monthly->pluck('ym'));
    const monthlyIncome = @json($monthly->pluck('income'));
    const monthlyExpense = @json($monthly->pluck('expense'));

    const memberRaw = @json($byMember);
    const memberLabels = [...new Set(memberRaw.map(x => x.label))];
    const memberIn = memberLabels.map(lbl => {
        const row = memberRaw.find(r => r.label === lbl && r.type === 'in');
        return row ? Number(row.total) : 0;
    });
    const memberOut = memberLabels.map(lbl => {
        const row = memberRaw.find(r => r.label === lbl && r.type === 'out');
        return row ? Number(row.total) : 0;
    });

    const walletRaw = @json($byWallet);
    const walletLabels = [...new Set(walletRaw.map(x => x.label))];
    const walletIn = walletLabels.map(lbl => {
        const row = walletRaw.find(r => r.label === lbl && r.type === 'in');
        return row ? Number(row.total) : 0;
    });
    const walletOut = walletLabels.map(lbl => {
        const row = walletRaw.find(r => r.label === lbl && r.type === 'out');
        return row ? Number(row.total) : 0;
    });

    // Load Chart.js
    $.getScript("{{ asset('assets/js/plugin/chart.js/chart.min.js') }}", function(){
        const ctxCat = document.getElementById('chartCategory').getContext('2d');
        const catColors = ['#4CAF50','#F44336','#2196F3','#FFC107','#9C27B0','#00BCD4','#8BC34A','#FF5722'];
        const categoryChart = new Chart(ctxCat, {
            type: 'doughnut',
            data: { labels: [], datasets: [{ data: [], backgroundColor: catColors }] },
            options: { responsive: true, legend: { position: 'bottom' } }
        });

        function renderCategoryChart(type) {
            const filtered = catRaw.filter(x => x.type === type);
            const labels = filtered.map(x => x.label);
            const data = filtered.map(x => Number(x.total));
            categoryChart.data.labels = labels;
            categoryChart.data.datasets[0].data = data;
            categoryChart.update();
        }

        // initial render
        renderCategoryChart($('#categoryTypeSelect').val());

        // on dropdown change
        $('#categoryTypeSelect').on('change', function(){
            renderCategoryChart($(this).val());
        });

        const ctxMonthly = document.getElementById('chartMonthly').getContext('2d');
        new Chart(ctxMonthly, {
            type: 'bar',
            data: { labels: monthlyLabels, datasets: [
                { label: 'IN', backgroundColor: '#4CAF50', data: monthlyIncome },
                { label: 'OUT', backgroundColor: '#F44336', data: monthlyExpense }
            ]},
            options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true } }] } }
        });

        const ctxMember = document.getElementById('chartMember').getContext('2d');
        new Chart(ctxMember, {
            type: 'bar',
            data: { labels: memberLabels, datasets: [
                { label: 'IN', backgroundColor: '#4CAF50', data: memberIn },
                { label: 'OUT', backgroundColor: '#F44336', data: memberOut }
            ]},
            options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true } }] } }
        });

        const ctxWallet = document.getElementById('chartWallet').getContext('2d');
        new Chart(ctxWallet, {
            type: 'bar',
            data: { labels: walletLabels, datasets: [
                { label: 'IN', backgroundColor: '#4CAF50', data: walletIn },
                { label: 'OUT', backgroundColor: '#F44336', data: walletOut }
            ]},
            options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true } }] } }
        });
    });
});
</script>
@endpush
