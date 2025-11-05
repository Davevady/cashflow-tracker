@extends('layouts.app')

@section('title', 'Tambah Anggota - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Tambah Anggota Keluarga</h4>
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
            <a href="#">Tambah</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Anggota Keluarga Baru</h4>
            </div>
            <form action="{{ route('members.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Anggota <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
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
                                  placeholder="Masukkan deskripsi anggota (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Jelaskan untuk siapa transaksi ini diperuntukkan
                        </small>
                    </div>
                </div>

                <div class="card-action">
                    <div class="form-group mb-3">
                        <label for="icon">Icon</label>
                        @include('components.icon-picker', ['name' => 'icon', 'value' => old('icon')])
                        @error('icon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('members.index') }}" class="btn btn-danger">
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
                    <i class="fa fa-info-circle"></i> Anggota keluarga digunakan untuk mengidentifikasi untuk siapa transaksi dilakukan.
                </p>
                <p class="text-muted">
                    Fitur ini membantu Anda melacak pengeluaran dan pemasukan berdasarkan anggota keluarga.
                </p>
                <p class="text-muted">
                    <strong>Contoh Penggunaan:</strong>
                </p>
                <ul class="text-muted">
                    <li><strong>Diri Sendiri</strong> - Transaksi pribadi</li>
                    <li><strong>Orang Tua</strong> - Belanja untuk orang tua</li>
                    <li><strong>Pasangan</strong> - Hadiah atau kebutuhan pasangan</li>
                    <li><strong>Anak</strong> - Biaya pendidikan, mainan, dll</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
