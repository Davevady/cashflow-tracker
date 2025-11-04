@extends('layouts.app')

@section('title', 'Transaksi - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Transaksi</h4>
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
            <a href="#">Transaksi</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Semua Transaksi</h4>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah Transaksi
                    </a>
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

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="12%">Tanggal</th>
                                <th width="15%">Kategori</th>
                                <th width="12%">Grup</th>
                                <th>Catatan</th>
                                <th width="15%" class="text-right">Jumlah</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>
                                    <div class="text-dark font-weight-bold">@shortIndonesianDate($transaction->created_at)</div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $transaction->category->type === 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->category->icon }} {{ $transaction->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $transaction->category->transactionGroup?->name ?? '-' }}</span>
                                </td>
                                <td>{{ $transaction->note ?? '-' }}</td>
                                <td class="text-right">
                                    <span class="text-{{ $transaction->category->type === 'income' ? 'success' : 'danger' }} font-weight-bold">
                                        {{ $transaction->category->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-primary btn-edit"
                                            data-id="{{ $transaction->id }}"
                                            data-category="{{ $transaction->category_id }}"
                                            data-amount="{{ $transaction->amount }}"
                                            data-date="{{ $transaction->date->format('Y-m-d\TH:i') }}"
                                            data-note="{{ $transaction->note }}"
                                            data-toggle="modal"
                                            data-target="#editModal">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                            data-id="{{ $transaction->id }}"
                                            data-toggle="modal"
                                            data-target="#deleteModal">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_category_id">Kategori</label>
                        <select class="form-control" id="edit_category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories->groupBy('transactionGroup.name') as $groupName => $groupCategories)
                                <optgroup label="{{ $groupName }}">
                                    @foreach($groupCategories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->icon }} {{ $category->name }} ({{ $category->type === 'income' ? 'Cash In' : 'Cash Out' }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_amount">Jumlah</label>
                        <input type="text" class="form-control" id="edit_amount" name="amount" placeholder="0" required>
                        <small class="form-text text-muted">Masukkan nominal tanpa titik atau koma</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_date">Tanggal & Waktu</label>
                        <input type="datetime-local" class="form-control" id="edit_date" name="date" required>
                        <small class="form-text text-muted">Format: 24 jam (00:00 - 23:59)</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_note">Catatan (Opsional)</label>
                        <textarea class="form-control" id="edit_note" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus transaksi ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle Edit button click
    $('.btn-edit').on('click', function() {
        const id = $(this).data('id');
        const categoryId = $(this).data('category');
        const amount = $(this).data('amount');
        const date = $(this).data('date');
        const note = $(this).data('note');

        // Set form action
        $('#editForm').attr('action', '/transactions/' + id);

        // Fill form fields
        $('#edit_category_id').val(categoryId);
        $('#edit_amount').val(amount);
        $('#edit_date').val(date);
        $('#edit_note').val(note);
    });

    // Handle Delete button click
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');

        // Set form action
        $('#deleteForm').attr('action', '/transactions/' + id);
    });
});
</script>
@endpush
