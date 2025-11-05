@extends('layouts.app')

@section('title', 'Tambah Kategori - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Tambah Kategori Transaksi</h4>
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
            <a href="{{ route('categories.index') }}">Kategori Transaksi</a>
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
                <h4 class="card-title">Form Kategori Transaksi Baru</h4>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="group_id">Grup Transaksi <span class="text-danger">*</span></label>
                        <select class="form-control @error('group_id') is-invalid @enderror"
                                id="group_id"
                                name="group_id"
                                required>
                            <option value="">-- Pilih Grup Transaksi --</option>
                            @foreach($transactionGroups->groupBy('type') as $type => $groups)
                                <optgroup label="{{ $type === 'in' ? 'Cash IN (Pemasukan)' : 'Cash OUT (Pengeluaran)' }}">
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('group_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Tipe kategori akan mengikuti tipe grup transaksi yang dipilih
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Masukkan nama kategori"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Contoh: Gaji, Bonus, Makanan, Transport, dll.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="Masukkan deskripsi kategori (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Jelaskan jenis transaksi yang termasuk dalam kategori ini
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
                    <a href="{{ route('categories.index') }}" class="btn btn-danger">
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
                    <i class="fa fa-info-circle"></i> Kategori digunakan untuk mengklasifikasikan transaksi berdasarkan jenisnya.
                </p>
                <p class="text-muted">
                    Setiap kategori harus berada dalam satu grup transaksi.
                </p>
                <p class="text-muted">
                    <strong>Contoh Struktur:</strong>
                </p>
                <ul class="text-muted">
                    <li><strong>Pemasukan</strong>
                        <ul>
                            <li>Gaji Bulanan</li>
                            <li>Bonus Tahunan</li>
                            <li>Hasil Investasi</li>
                        </ul>
                    </li>
                    <li><strong>Pengeluaran</strong>
                        <ul>
                            <li>Makanan & Minuman</li>
                            <li>Transportasi</li>
                            <li>Belanja Bulanan</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

