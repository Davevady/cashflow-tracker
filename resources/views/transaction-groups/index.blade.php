@extends('layouts.app')

@section('title', 'Grup Transaksi - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Grup Transaksi</h4>
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
            <a href="#">Grup Transaksi</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Semua Grup Transaksi</h4>
                    <a href="{{ route('transaction-groups.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah Grup Transaksi
                    </a>
                </div>
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

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Tipe</th>
                                <th width="18%">Nama Grup</th>
                                <th width="35%">Deskripsi</th>
                                <th width="10%">Jumlah Kategori</th>
                                <th width="14%">Dibuat</th>
                                <th width="8%" class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactionGroups as $index => $group)
                                <tr>
                                    <td>{{ $transactionGroups->firstItem() + $index }}</td>
                                    <td>
                                        @if($group->type === 'in')
                                            <span class="badge badge-success"><i class="fa fa-arrow-down"></i> IN</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fa fa-arrow-up"></i> OUT</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $group->name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $group->description ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $group->categories_count }} kategori</span>
                                    </td>
                                    <td>
                                        <small>@shortIndonesianDate($group->created_at)</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('transaction-groups.edit', $group) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('transaction-groups.destroy', $group) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus grup ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted">Belum ada grup transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactionGroups->hasPages())
                    <div class="mt-3">
                        {{ $transactionGroups->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
