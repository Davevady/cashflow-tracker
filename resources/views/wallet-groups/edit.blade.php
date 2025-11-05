@extends('layouts.app')

@section('title', 'Edit Grup Dompet')

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Grup Dompet</h4>
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
            <a href="#">Edit</a>
        </li>
    </ul>
    </div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Form Edit Grup Dompet</h4>
                    <a href="{{ route('wallet-groups.index') }}" class="btn btn-secondary btn-round ml-auto">Kembali</a>
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
        <form method="POST" action="{{ route('wallet-groups.update', $group) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $group->name) }}">
                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Icon</label>
                @include('components.icon-picker', ['name' => 'icon', 'value' => old('icon', $group->icon)])
                @error('icon')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control">{{ old('description', $group->description) }}</textarea>
                @error('description')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <button class="btn btn-primary">Update</button>
        </form>
            </div>
        </div>
    </div>
</div>
@endsection


