@extends('layouts.app')

@section('title', 'Dompet')

@section('content')
<div class="page-header">
    <h4 class="page-title">Dompet</h4>
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
            <a href="#">Dompet</a>
        </li>
    </ul>
    </div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Semua Dompet</h4>
                    <a href="{{ route('wallets.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah Dompet
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

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Grup</th>
                                <th>Saldo</th>
                                <th>Status</th>
                                <th class="text-right" style="width:150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wallets as $wallet)
                                <tr>
                                    <td>{{ $wallet->name }}</td>
                                    <td>{{ $wallet->walletGroup->name }}</td>
                                    <td><span class="money-text" data-amount="{{ $wallet->balance }}"></span></td>
                                    <td>
                                        @if($wallet->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('wallets.edit', $wallet) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                                            <form action="{{ route('wallets.destroy', $wallet) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus dompet ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Hapus"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($wallets->hasPages())
                    <div class="mt-3">
                        {{ $wallets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


