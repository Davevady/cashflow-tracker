@extends('layouts.app')

@section('title', 'Tambah Grup Transaksi - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Tambah Grup Transaksi</h4>
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
            <a href="{{ route('transaction-groups.index') }}">Grup Transaksi</a>
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
                <h4 class="card-title">Form Grup Transaksi Baru</h4>
            </div>
            <form action="{{ route('transaction-groups.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="type">Tipe Transaksi <span class="text-danger">*</span></label>
                        <select class="form-control @error('type') is-invalid @enderror"
                                id="type"
                                name="type"
                                required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Cash IN (Pemasukan)</option>
                            <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Cash OUT (Pengeluaran)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Pilih apakah grup ini untuk pemasukan atau pengeluaran
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Grup <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Masukkan nama grup transaksi"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Contoh: Pendapatan, Investasi, Belanja, Transport, dll.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="Masukkan deskripsi grup (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Jelaskan fungsi atau tujuan dari grup transaksi ini
                        </small>
                    </div>
                </div>

                <div class="card-action">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('transaction-groups.index') }}" class="btn btn-danger">
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
                    <i class="fa fa-info-circle"></i> Grup transaksi digunakan untuk mengelompokkan kategori transaksi.
                </p>
                <p class="text-muted">
                    Contoh struktur:
                </p>
                <ul class="text-muted">
                    <li><strong>Pemasukan</strong>
                        <ul>
                            <li>Gaji</li>
                            <li>Bonus</li>
                            <li>Investasi</li>
                        </ul>
                    </li>
                    <li><strong>Pengeluaran</strong>
                        <ul>
                            <li>Makanan</li>
                            <li>Transport</li>
                            <li>Belanja</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
