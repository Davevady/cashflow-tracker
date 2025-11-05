@extends('layouts.app')

@section('title', 'Kategori Transaksi - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Kategori Transaksi</h4>
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
            <a href="#">Kategori Transaksi</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Semua Kategori Transaksi</h4>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah Kategori
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
                                <th width="18%">Nama Kategori</th>
                                <th width="15%">Grup Transaksi</th>
                                <th width="25%">Deskripsi</th>
                                <th width="10%">Jumlah Transaksi</th>
                                <th width="12%">Dibuat</th>
                                <th width="5%" class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $category)
                                <tr>
                                    <td>{{ $categories->firstItem() + $index }}</td>
                                    <td>
                                        @if($category->transactionGroup->type === 'in')
                                            <span class="badge badge-success"><i class="fa fa-arrow-down"></i> IN</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fa fa-arrow-up"></i> OUT</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $category->transactionGroup->name }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $category->description ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $category->transactions_count }} transaksi</span>
                                    </td>
                                    <td>
                                        <small class="datetime-text" data-datetime="{{ $category->created_at }}">{{ $category->created_at }}</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('categories.edit', $category) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                                    <td colspan="8" class="text-center py-4">
                                        <p class="text-muted">Belum ada kategori transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="mt-3">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
