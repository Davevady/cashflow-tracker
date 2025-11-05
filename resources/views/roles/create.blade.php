@extends('layouts.app')

@section('title', 'Tambah Role - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Tambah Role</h4>
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
            <a href="{{ route('roles.index') }}">Role</a>
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Tambah Role</h4>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Role <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Masukkan nama role"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Contoh: admin, manager, staff, dll.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="guard_name">Guard Name</label>
                        <input type="text"
                               class="form-control @error('guard_name') is-invalid @enderror"
                               id="guard_name"
                               name="guard_name"
                               value="{{ old('guard_name', 'web') }}"
                               readonly>
                        @error('guard_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Default: web (tidak perlu diubah)
                        </small>
                    </div>
                </div>

                <div class="card-action">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-danger">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h4 class="card-title">Informasi</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="fa fa-info-circle"></i> Role adalah peran yang dapat diberikan kepada user.
                </p>
                <p class="text-muted">
                    Setelah membuat role, Anda dapat menambahkan permission ke role tersebut di halaman daftar role.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
