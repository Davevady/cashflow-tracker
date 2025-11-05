@extends('layouts.app')

@section('title', 'Tambah Grup Dompet')

@section('content')
<div class="page-header">
    <h4 class="page-title">Tambah Grup Dompet</h4>
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
            <a href="{{ route('wallet-groups.index') }}">Grup Dompet</a>
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
                    <h4 class="card-title">Form Tambah Grup Dompet</h4>
                    <a href="{{ route('wallet-groups.index') }}" class="btn btn-secondary btn-round ml-auto">Kembali</a>
                </div>
            </div>
            <div class="card-body">
        <form method="POST" action="{{ route('wallet-groups.store') }}">
            @csrf
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Icon</label>
                @include('components.icon-picker', ['name' => 'icon', 'value' => old('icon')])
                @error('icon')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                @error('description')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <button class="btn btn-primary">Simpan</button>
        </form>
            </div>
        </div>
    </div>
</div>
@endsection


