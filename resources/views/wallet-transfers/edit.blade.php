@extends('layouts.app')

@section('title', 'Edit Transfer')

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Transfer</h4>
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
            <a href="#">Edit</a>
        </li>
    </ul>
    </div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Form Edit Transfer</h4>
                    <a href="{{ route('wallet-transfers.index') }}" class="btn btn-secondary btn-round ml-auto">Kembali</a>
                </div>
            </div>
            <div class="card-body">
        <form method="POST" action="{{ route('wallet-transfers.update', $transfer) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Dari Dompet</label>
                <select name="from_wallet_id" class="form-control" required>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" {{ old('from_wallet_id', $transfer->from_wallet_id) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
                @error('from_wallet_id')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Ke Dompet</label>
                <select name="to_wallet_id" class="form-control" required>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" {{ old('to_wallet_id', $transfer->to_wallet_id) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
                @error('to_wallet_id')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" step="0.01" name="amount" class="form-control" required value="{{ old('amount', $transfer->amount) }}">
                @error('amount')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Biaya</label>
                <input type="number" step="0.01" name="fee" class="form-control" value="{{ old('fee', $transfer->fee) }}">
                @error('fee')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Tanggal Transfer</label>
                <input type="datetime-local" name="transfer_date" class="form-control" required value="{{ old('transfer_date', $transfer->transfer_date->format('Y-m-d\TH:i')) }}">
                @error('transfer_date')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea name="note" class="form-control">{{ old('note', $transfer->note) }}</textarea>
                @error('note')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <button class="btn btn-primary">Update</button>
        </form>
            </div>
        </div>
    </div>
</div>
@endsection


