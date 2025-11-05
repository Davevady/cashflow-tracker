@extends('layouts.app')

@section('title', 'Edit Role - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Role</h4>
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
            <a href="#">Edit</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Edit Role</h4>
            </div>
            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Role <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $role->name) }}"
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
                               value="{{ old('guard_name', $role->guard_name) }}"
                               readonly>
                        @error('guard_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Default: web (tidak perlu diubah)
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Informasi</label>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Dibuat:</strong> @shortIndonesianDate($role->created_at)
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Diperbarui:</strong> @shortIndonesianDate($role->updated_at)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-action">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Perbarui
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-danger">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-warning">
            <div class="card-header">
                <h4 class="card-title">Peringatan</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="fa fa-exclamation-triangle"></i> Mengubah nama role akan mempengaruhi semua user yang terkait.
                </p>
                <p class="text-muted">
                    Role ini memiliki <strong>{{ $role->users()->count() }} user</strong> yang terkait.
                </p>
            </div>
        </div>

        <div class="card card-info">
            <div class="card-header">
                <h4 class="card-title">Kelola Permission</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Untuk mengelola permission role ini, gunakan tombol di bawah.
                </p>
                <a href="{{ route('roles.permissions', $role) }}" class="btn btn-info btn-block">
                    <i class="fa fa-key"></i> Kelola Permission
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
