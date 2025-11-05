@extends('layouts.app')

@section('title', 'Edit Dompet')

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Dompet</h4>
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
            <a href="{{ route('wallets.index') }}">Dompet</a>
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
                    <h4 class="card-title">Form Edit Dompet</h4>
                    <a href="{{ route('wallets.index') }}" class="btn btn-secondary btn-round ml-auto">Kembali</a>
                </div>
            </div>
            <div class="card-body">
        <form method="POST" action="{{ route('wallets.update', $wallet) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Grup Dompet</label>
                <select name="wallet_group_id" class="form-control" required>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ old('wallet_group_id', $wallet->wallet_group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('wallet_group_id')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $wallet->name) }}">
                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>No. Akun/Rekening</label>
                <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $wallet->account_number) }}">
                @error('account_number')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Saldo</label>
                <input type="text" name="balance" class="form-control rupiah-input" value="{{ old('balance', $wallet->balance) }}">
                @error('balance')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Icon</label>
                @include('components.icon-picker', ['name' => 'icon', 'value' => old('icon', $wallet->icon)])
                @error('icon')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control">{{ old('description', $wallet->description) }}</textarea>
                @error('description')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $wallet->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
            <button class="btn btn-primary">Update</button>
        </form>
            </div>
        </div>
    </div>
</div>
@endsection


