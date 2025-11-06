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
                                <th width="8%">Tipe</th>
                                <th width="12%">Tanggal</th>
                                <th width="15%">Kategori</th>
                                <th width="10%">Grup</th>
                                <th>Dompet</th>
                                <th>Anggota</th>
                                <th>Catatan</th>
                                <th width="14%" class="text-right">Jumlah</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>
                                    @if($transaction->category->transactionGroup->type === 'in')
                                        <span class="badge badge-success"><i class="fa fa-arrow-down"></i> IN</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fa fa-arrow-up"></i> OUT</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark font-weight-bold">@shortIndonesianDate($transaction->created_at)</div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $transaction->category->transactionGroup->type === 'in' ? 'success' : 'danger' }}">
                                        <i class="mr-1 {{ $transaction->category->icon }}"></i>{{ $transaction->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $transaction->category->transactionGroup?->name ?? '-' }}</span>
                                </td>
                                <td>{{ $transaction->wallet?->name ?? '-' }}</td>
                                <td>{{ $transaction->member?->name ?? '-' }}</td>
                                <td>{{ $transaction->note ?? '-' }}</td>
                                <td class="text-right">
                                    <span class="text-{{ $transaction->category->transactionGroup->type === 'in' ? 'success' : 'danger' }} font-weight-bold">
                                        {{ $transaction->category->transactionGroup->type === 'in' ? '+' : '-' }} <span class="money-text" data-amount="{{ $transaction->amount }}"></span>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-id="{{ $transaction->id }}"
                                            data-category="{{ $transaction->category_id }}"
                                            data-amount="{{ $transaction->amount }}"
                                            data-wallet="{{ $transaction->wallet_id }}"
                                            data-member="{{ $transaction->member_id }}"
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
                                <td colspan="7" class="text-center">Belum ada transaksi</td>
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
                    <!-- Transaction Type Toggle -->
                    <div class="form-group">
                        <label class="font-weight-bold">Tipe Transaksi</label>
                        <div class="btn-group btn-group-toggle w-100 transaction-type-toggle-edit" data-toggle="buttons">
                            <label class="btn btn-success" id="btn-edit-income">
                                <input type="radio" name="edit_type_toggle" value="in">
                                <i class="fa fa-arrow-down"></i> Cash In
                            </label>
                            <label class="btn btn-danger" id="btn-edit-expense">
                                <input type="radio" name="edit_type_toggle" value="out">
                                <i class="fa fa-arrow-up"></i> Cash Out
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_category_id">Kategori</label>
                        <div class="input-group align-items-center">
                            <select class="form-control mr-3" id="edit_category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories->filter(fn($c) => $c->transactionGroup->type === 'in')->groupBy('transactionGroup.name') as $groupName => $groupCategories)
                                    <optgroup label="{{ $groupName }}" class="category-group-edit in-category-edit">
                                        @foreach($groupCategories as $category)
                                            <option value="{{ $category->id }}" data-type="in" data-icon="{{ $category->icon }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                @foreach($categories->filter(fn($c) => $c->transactionGroup->type === 'out')->groupBy('transactionGroup.name') as $groupName => $groupCategories)
                                    <optgroup label="{{ $groupName }}" class="category-group-edit out-category-edit" style="display: none;">
                                        @foreach($groupCategories as $category)
                                            <option value="{{ $category->id }}" data-type="out" data-icon="{{ $category->icon }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <i id="selectedIcon" class="fa"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_wallet_id">Dompet</label>
                        <select class="form-control" id="edit_wallet_id" name="wallet_id" required>
                            <option value="">Pilih Dompet</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->walletGroup->name }} - {{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_member_id">Anggota</label>
                        <div class="input-group align-items-center">
                            <select class="form-control mr-3" id="edit_member_id" name="member_id">
                                <option value="">Pilih Anggota (opsional)</option>
                                @foreach($members as $m)
                                    <option value="{{ $m->id }}" data-icon="{{ $m->icon }}">
                                        {{ $m->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i id="selectedMemberIcon" class="fa"></i>
                        </div>
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

@push('styles')
<style>
    /* Custom styling for transaction type toggle in edit modal */
    .transaction-type-toggle-edit {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .transaction-type-toggle-edit .btn {
        width: 50%;
        border-radius: 0;
        border: none;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        position: relative;
        opacity: 0.6;
    }

    .transaction-type-toggle-edit .btn:first-child {
        border-top-left-radius: 0.5rem;
        border-bottom-left-radius: 0.5rem;
    }

    .transaction-type-toggle-edit .btn:last-child {
        border-top-right-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    .transaction-type-toggle-edit .btn:not(.active) {
        background-color: #f5f5f5 !important;
        color: #666 !important;
    }

    .transaction-type-toggle-edit .btn.active {
        opacity: 1;
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .transaction-type-toggle-edit .btn-success.active {
        color: white !important;
    }

    .transaction-type-toggle-edit .btn-danger.active {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%) !important;
        color: white !important;
    }

    .transaction-type-toggle-edit .btn i {
        margin-right: 5px;
        font-size: 16px;
    }

    .transaction-type-toggle-edit .btn:hover:not(.active) {
        opacity: 0.8;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Transaction type toggle functionality for edit modal
    $('input[name="edit_type_toggle"]').on('change', function() {
        const selectedType = $(this).val();
        const categorySelect = $('#edit_category_id');
        const $parentLabel = $(this).parent();

        // Update active state visually
        $('.transaction-type-toggle-edit .btn').removeClass('active');
        $parentLabel.addClass('active');

        // Reset select value
        const currentValue = categorySelect.val();
        categorySelect.val('');

        // Hide all categories first
        categorySelect.find('option[data-type]').hide();
        categorySelect.find('optgroup').hide();

        // Show categories for selected type
        if (selectedType === 'in') {
            categorySelect.find('option[data-type="in"]').show();
            categorySelect.find('.in-category-edit').show();
        } else {
            categorySelect.find('option[data-type="out"]').show();
            categorySelect.find('.out-category-edit').show();
        }

        // Try to keep the same category if it matches the type
        const selectedOption = categorySelect.find(`option[value="${currentValue}"]`);
        if (selectedOption.length && selectedOption.data('type') === selectedType) {
            categorySelect.val(currentValue);
        }

        // Update icon
        updateCategoryIcon();
    });

    // Handle label click to trigger change
    $('.transaction-type-toggle-edit .btn').on('click', function() {
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
    });

    // Update category icon
    function updateCategoryIcon() {
        const categoryIcon = $('#edit_category_id option:selected').data('icon');
        $('#selectedIcon').attr('class', categoryIcon || 'fa');
    }

    // Update icon when category changes
    $('#edit_category_id').on('change', function() {
        updateCategoryIcon();
    });

    // Handle Edit button click
    $('.btn-edit').on('click', function() {
        const id = $(this).data('id');
        const categoryId = $(this).data('category');
        const amount = $(this).data('amount');
        const walletId = $(this).data('wallet');
        const memberId = $(this).data('member');
        const date = $(this).data('date');
        const note = $(this).data('note');

        $('#editForm').attr('action', '/transactions/' + id);

        // Get category type to set toggle
        const selectedCategoryOption = $(`#edit_category_id option[value="${categoryId}"]`);
        const categoryType = selectedCategoryOption.data('type');

        // Set transaction type toggle
        if (categoryType === 'in') {
            $('#btn-edit-income input').prop('checked', true);
            $('#btn-edit-income').addClass('active');
            $('#btn-edit-expense').removeClass('active');
            $('input[name="edit_type_toggle"][value="in"]').trigger('change');
        } else {
            $('#btn-edit-expense input').prop('checked', true);
            $('#btn-edit-expense').addClass('active');
            $('#btn-edit-income').removeClass('active');
            $('input[name="edit_type_toggle"][value="out"]').trigger('change');
        }

        // Set form values
        $('#edit_category_id').val(categoryId);
        $('#edit_amount').val(amount);
        $('#edit_wallet_id').val(walletId);
        $('#edit_member_id').val(memberId);
        $('#edit_date').val(date);
        $('#edit_note').val(note);

        // Update icons
        updateCategoryIcon();
        const memberIcon = $('#edit_member_id option:selected').data('icon');
        $('#selectedMemberIcon').attr('class', memberIcon || 'fa');
    });

    // update ikon saat dropdown member berubah
    $('#edit_member_id').on('change', function() {
        const icon = $(this).find(':selected').data('icon');
        $('#selectedMemberIcon').attr('class', icon || 'fa');
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
