@extends('layouts.app')

@section('title', 'Edit Kategori - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Edit Kategori Transaksi</h4>
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
            <a href="#">Edit</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Edit Kategori Transaksi</h4>
            </div>
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
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
                                        <option value="{{ $group->id }}" {{ old('group_id', $category->group_id) == $group->id ? 'selected' : '' }}>
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
                               value="{{ old('name', $category->name) }}"
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
                                  placeholder="Masukkan deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Jelaskan jenis transaksi yang termasuk dalam kategori ini
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon</label>
                        @include('components.icon-picker', ['name' => 'icon', 'value' => old('icon', $category->icon)])
                        @error('icon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Informasi</label>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Dibuat:</strong> @shortIndonesianDate($category->created_at)
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Diperbarui:</strong> @shortIndonesianDate($category->updated_at)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-action">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Perbarui
                    </button>
                    <a href="{{ route('categories.index') }}" class="btn btn-danger">
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
                    <i class="fa fa-exclamation-triangle"></i> Mengubah kategori transaksi akan mempengaruhi semua transaksi yang terkait.
                </p>
                <p class="text-muted">
                    Kategori ini memiliki <strong>{{ $category->transactions()->count() }} transaksi</strong> yang terkait.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

