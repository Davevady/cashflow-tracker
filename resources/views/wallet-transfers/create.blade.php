@extends('layouts.app')

@section('title', 'Transfer Baru')

@section('content')
<div class="page-header">
    <h4 class="page-title">Transfer Baru</h4>
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
            <a href="{{ route('wallet-transfers.index') }}">Transfer Antar Dompet</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Tambah</a>
        </li>
    </ul>
    </div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Form Transfer Baru</h4>
                    <a href="{{ route('wallet-transfers.index') }}" class="btn btn-secondary btn-round ml-auto">Kembali</a>
                </div>
            </div>
            <div class="card-body">
        <form method="POST" action="{{ route('wallet-transfers.store') }}">
            @csrf
            <div class="form-group">
                <label>Dari Dompet</label>
                <select name="from_wallet_id" class="form-control" required>
                    <option value="">-- Pilih Dompet --</option>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" {{ old('from_wallet_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
                @error('from_wallet_id')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Ke Dompet</label>
                <select name="to_wallet_id" class="form-control" required>
                    <option value="">-- Pilih Dompet --</option>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" {{ old('to_wallet_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
                @error('to_wallet_id')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" step="0.01" name="amount" class="form-control" required value="{{ old('amount') }}">
                @error('amount')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Biaya</label>
                <input type="number" step="0.01" name="fee" class="form-control" value="{{ old('fee', 0) }}">
                @error('fee')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Tanggal Transfer</label>
                <input type="datetime-local" name="transfer_date" class="form-control" required value="{{ old('transfer_date') }}">
                @error('transfer_date')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                @error('note')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <button class="btn btn-primary">Simpan</button>
        </form>
            </div>
        </div>
    </div>
</div>
@endsection


