@extends('layouts.app')

@section('title', 'Transfer Antar Dompet')

@section('content')
<div class="page-header">
    <h4 class="page-title">Transfer Antar Dompet</h4>
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
            <a href="#">Transfer Antar Dompet</a>
        </li>
    </ul>
    </div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Daftar Transfer</h4>
                    <a href="{{ route('wallet-transfers.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Transfer Baru
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
                                <th>Tanggal</th>
                                <th>Dari</th>
                                <th>Ke</th>
                                <th>Jumlah</th>
                                <th>Biaya</th>
                                <th>Catatan</th>
                                <th class="text-right" style="width:150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td><span class="datetime-text" data-datetime="{{ $transfer->transfer_date }}">{{ $transfer->transfer_date }}</span></td>
                                    <td>{{ $transfer->fromWallet->name }}</td>
                                    <td>{{ $transfer->toWallet->name }}</td>
                                    <td><span class="money-text" data-amount="{{ $transfer->amount }}"></span></td>
                                    <td><span class="money-text" data-amount="{{ $transfer->fee }}"></span></td>
                                    <td>{{ $transfer->note }}</td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('wallet-transfers.edit', $transfer) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                                            <form action="{{ route('wallet-transfers.destroy', $transfer) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus transfer ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Hapus"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transfers->hasPages())
                    <div class="mt-3">
                        {{ $transfers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


