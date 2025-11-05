@extends('layouts.app')

@section('title', 'Tempat Sampah')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fa fa-trash"></i> Tempat Sampah</h2>
            <p class="text-muted mb-0">Data yang telah dihapus dapat dipulihkan atau dihapus permanen</p>
        </div>
        <form action="{{ route('trash.empty') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan semua tempat sampah? Data akan dihapus permanen dan tidak dapat dipulihkan!');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fa fa-trash"></i> Kosongkan Semua Sampah
            </button>
        </form>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="trashTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="transactions-tab" data-toggle="tab" href="#transactions" role="tab">
                <i class="fa fa-exchange-alt"></i> Transaksi <span class="badge badge-secondary">{{ $deletedTransactions->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="categories-tab" data-toggle="tab" href="#categories" role="tab">
                <i class="fa fa-tags"></i> Kategori <span class="badge badge-secondary">{{ $deletedCategories->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="transaction-groups-tab" data-toggle="tab" href="#transaction-groups" role="tab">
                <i class="fa fa-layer-group"></i> Grup Transaksi <span class="badge badge-secondary">{{ $deletedTransactionGroups->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="wallets-tab" data-toggle="tab" href="#wallets" role="tab">
                <i class="fa fa-wallet"></i> Dompet <span class="badge badge-secondary">{{ $deletedWallets->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="wallet-groups-tab" data-toggle="tab" href="#wallet-groups" role="tab">
                <i class="fa fa-folder"></i> Grup Dompet <span class="badge badge-secondary">{{ $deletedWalletGroups->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="transfers-tab" data-toggle="tab" href="#transfers" role="tab">
                <i class="fa fa-exchange-alt"></i> Transfer <span class="badge badge-secondary">{{ $deletedWalletTransfers->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="members-tab" data-toggle="tab" href="#members" role="tab">
                <i class="fa fa-users"></i> Anggota <span class="badge badge-secondary">{{ $deletedMembers->count() }}</span>
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="trashTabContent">
        <!-- Transactions Tab -->
        <div class="tab-pane fade show active" id="transactions" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($deletedTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Dompet</th>
                                    <th>Anggota</th>
                                    <th>Nominal</th>
                                    <th>Dihapus Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedTransactions as $transaction)
                                <tr>
                                    <td>@shortIndonesianDate($transaction->date)</td>
                                    <td>
                                        @if($transaction->category)
                                            {{ $transaction->category->icon }} {{ $transaction->category->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->wallet?->name ?? '-' }}</td>
                                    <td>{{ $transaction->member?->name ?? '-' }}</td>
                                    <td><span class="money-text" data-amount="{{ $transaction->amount }}"></span></td>
                                    <td>@shortIndonesianDate($transaction->deleted_at)</td>
                                    <td>
                                        <form action="{{ route('trash.restore') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="type" value="transaction">
                                            <input type="hidden" name="id" value="{{ $transaction->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('trash.forceDelete') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Data tidak dapat dipulihkan!');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="transaction">
                                            <input type="hidden" name="id" value="{{ $transaction->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada transaksi yang dihapus</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Categories Tab -->
        <div class="tab-pane fade" id="categories" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($deletedCategories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Grup Transaksi</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Dihapus Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedCategories as $category)
                                <tr>
                                    <td>{{ $category->icon }} {{ $category->name }}</td>
                                    <td>{{ $category->transactionGroup?->name ?? '-' }}</td>
                                    <td>{{ $category->transactions_count }}</td>
                                    <td>@shortIndonesianDate($category->deleted_at)</td>
                                    <td>
                                        <form action="{{ route('trash.restore') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="type" value="category">
                                            <input type="hidden" name="id" value="{{ $category->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('trash.forceDelete') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Data tidak dapat dipulihkan!');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="category">
                                            <input type="hidden" name="id" value="{{ $category->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada kategori yang dihapus</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaction Groups Tab -->
        <div class="tab-pane fade" id="transaction-groups" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($deletedTransactionGroups->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Jumlah Kategori</th>
                                    <th>Dihapus Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedTransactionGroups as $group)
                                <tr>
                                    <td>{{ $group->name }}</td>
                                    <td>
                                        @if($group->type === 'in')
                                            <span class="badge badge-success"><i class="fa fa-arrow-down"></i> IN</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fa fa-arrow-up"></i> OUT</span>
                                        @endif
                                    </td>
                                    <td>{{ $group->categories_count }}</td>
                                    <td>@shortIndonesianDate($group->deleted_at)</td>
                                    <td>
                                        <form action="{{ route('trash.restore') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="type" value="transaction_group">
                                            <input type="hidden" name="id" value="{{ $group->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('trash.forceDelete') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Data tidak dapat dipulihkan!');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="transaction_group">
                                            <input type="hidden" name="id" value="{{ $group->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada grup transaksi yang dihapus</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Wallets Tab -->
        <div class="tab-pane fade" id="wallets" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($deletedWallets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Grup</th>
                                    <th>Saldo</th>
                                    <th>Dihapus Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedWallets as $wallet)
                                <tr>
                                    <td>{{ $wallet->icon }} {{ $wallet->name }}</td>
                                    <td>{{ $wallet->walletGroup?->name ?? '-' }}</td>
                                    <td><span class="money-text" data-amount="{{ $wallet->balance }}"></span></td>
                                    <td>@shortIndonesianDate($wallet->deleted_at)</td>
                                    <td>
                                        <form action="{{ route('trash.restore') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="type" value="wallet">
                                            <input type="hidden" name="id" value="{{ $wallet->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('trash.forceDelete') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Data tidak dapat dipulihkan!');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="wallet">
                                            <input type="hidden" name="id" value="{{ $wallet->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada dompet yang dihapus</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Wallet Groups Tab -->
        <div class="tab-pane fade" id="wallet-groups" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($deletedWalletGroups->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jumlah Dompet</th>
                                    <th>Dihapus Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedWalletGroups as $group)
                                <tr>
                                    <td>{{ $group->icon }} {{ $group->name }}</td>
                                    <td>{{ $group->wallets_count }}</td>
                                    <td>@shortIndonesianDate($group->deleted_at)</td>
                                    <td>
                                        <form action="{{ route('trash.restore') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="type" value="wallet_group">
                                            <input type="hidden" name="id" value="{{ $group->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('trash.forceDelete') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Data tidak dapat dipulihkan!');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="wallet_group">
                                            <input type="hidden" name="id" value="{{ $group->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada grup dompet yang dihapus</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transfers Tab -->
        <div class="tab-pane fade" id="transfers" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($deletedWalletTransfers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Dari</th>
                                    <th>Ke</th>
                                    <th>Nominal</th>
                                    <th>Biaya</th>
                                    <th>Tanggal</th>
                                    <th>Dihapus Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedWalletTransfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->fromWallet?->name ?? '-' }}</td>
                                    <td>{{ $transfer->toWallet?->name ?? '-' }}</td>
                                    <td><span class="money-text" data-amount="{{ $transfer->amount }}"></span></td>
                                    <td><span class="money-text" data-amount="{{ $transfer->fee }}"></span></td>
                                    <td>@shortIndonesianDate($transfer->transfer_date)</td>
                                    <td>@shortIndonesianDate($transfer->deleted_at)</td>
                                    <td>
                                        <form action="{{ route('trash.restore') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="type" value="wallet_transfer">
                                            <input type="hidden" name="id" value="{{ $transfer->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('trash.forceDelete') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Data tidak dapat dipulihkan!');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="wallet_transfer">
                                            <input type="hidden" name="id" value="{{ $transfer->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada transfer yang dihapus</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Members Tab -->
        <div class="tab-pane fade" id="members" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($deletedMembers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Dihapus Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedMembers as $member)
                                <tr>
                                    <td>{{ $member->icon }} {{ $member->name }}</td>
                                    <td>{{ $member->transactions_count }}</td>
                                    <td>@shortIndonesianDate($member->deleted_at)</td>
                                    <td>
                                        <form action="{{ route('trash.restore') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="type" value="member">
                                            <input type="hidden" name="id" value="{{ $member->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('trash.forceDelete') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Data tidak dapat dipulihkan!');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="member">
                                            <input type="hidden" name="id" value="{{ $member->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada anggota yang dihapus</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
