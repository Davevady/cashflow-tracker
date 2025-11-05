@extends('layouts.app')

@section('title', 'Edit Anggota - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Anggota Keluarga</h4>
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
            <a href="{{ route('members.index') }}">Anggota Keluarga</a>
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
            <!-- Form Edit -->
            <div class="card-header">
                <h4 class="card-title">Form Edit Anggota Keluarga</h4>
            </div>
            <form action="{{ route('members.update', $member) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Anggota <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $member->name) }}"
                               placeholder="Masukkan nama anggota keluarga"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Contoh: Diri Sendiri, Orang Tua, Pasangan, Anak, dll.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="Masukkan deskripsi anggota (opsional)">{{ old('description', $member->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Jelaskan untuk siapa transaksi ini diperuntukkan
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon</label>
                        @include('components.icon-picker', ['name' => 'icon', 'value' => old('icon', $member->icon)])
                        @error('icon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Informasi</label>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Dibuat:</strong> @shortIndonesianDate($member->created_at)
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Diperbarui:</strong> @shortIndonesianDate($member->updated_at)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-action">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Perbarui
                    </button>
                    <a href="{{ route('members.index') }}" class="btn btn-danger">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
    </div>

    <div class="col-md-4">
        <div class="card card-warning">
            <div class="card-header">
                <h4 class="card-title">Peringatan</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="fa fa-exclamation-triangle"></i>
                    Mengubah data anggota keluarga akan mempengaruhi semua transaksi yang terkait.
                </p>
                <p class="text-muted">
                    Anggota ini memiliki
                    <strong>{{ $member->transactions()->count() }} transaksi</strong>
                    yang terkait.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
