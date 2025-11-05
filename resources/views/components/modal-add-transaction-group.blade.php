<div class="modal fade" id="modalAddTransactionGroup" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Grup Transaksi Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddTransactionGroup">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_transaction_group_name">Nama Grup <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_transaction_group_name" name="name" required>
                        <small id="error_transaction_group_name" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label for="new_transaction_group_type">Tipe <span class="text-danger">*</span></label>
                        <select class="form-control" id="new_transaction_group_type" name="type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="in">Cash IN (Pemasukan)</option>
                            <option value="out">Cash OUT (Pengeluaran)</option>
                        </select>
                        <small id="error_transaction_group_type" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label for="new_transaction_group_icon">Icon</label>
                        @include('components.icon-picker', ['name' => 'icon', 'value' => 'fa fa-tags', 'id' => 'new_transaction_group'])
                        <small id="error_transaction_group_icon" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label for="new_transaction_group_description">Deskripsi</label>
                        <textarea class="form-control" id="new_transaction_group_description" name="description" rows="3"></textarea>
                        <small id="error_transaction_group_description" class="text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveTransactionGroup">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#formAddTransactionGroup').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.text-danger').text('');
        $('#btnSaveTransactionGroup').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '{{ route("transaction-groups.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    const group = response.data;
                    const typeLabel = group.type === 'in' ? 'Cash IN (Pemasukan)' : 'Cash OUT (Pengeluaran)';

                    // Find or create optgroup
                    let optgroup = $(`#group_id optgroup[label="${typeLabel}"]`);
                    if (optgroup.length === 0) {
                        optgroup = $('<optgroup>').attr('label', typeLabel);
                        $('#group_id').append(optgroup);
                    }

                    // Add new option to optgroup
                    const newOption = new Option(group.name, group.id, true, true);
                    optgroup.append(newOption);

                    $('#group_id').val(group.id).trigger('change');

                    // Close modal and reset form
                    $('#modalAddTransactionGroup').modal('hide');
                    $('#formAddTransactionGroup')[0].reset();

                    // Show success notification
                    $.notify({
                        icon: 'fa fa-check',
                        message: 'Grup transaksi berhasil ditambahkan!'
                    }, {
                        type: 'success',
                        placement: {
                            from: 'top',
                            align: 'right'
                        }
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    for (const [field, messages] of Object.entries(errors)) {
                        $(`#error_transaction_group_${field}`).text(messages[0]);
                    }
                } else {
                    $.notify({
                        icon: 'fa fa-times',
                        message: 'Gagal menambahkan grup transaksi!'
                    }, {
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        }
                    });
                }
            },
            complete: function() {
                $('#btnSaveTransactionGroup').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
            }
        });
    });

    // Reset form when modal is closed
    $('#modalAddTransactionGroup').on('hidden.bs.modal', function() {
        $('#formAddTransactionGroup')[0].reset();
        $('.text-danger').text('');
    });
});
</script>
@endpush
