<div class="modal fade" id="modalAddWalletGroup" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Grup Dompet Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddWalletGroup">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_wallet_group_name">Nama Grup <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_wallet_group_name" name="name" required>
                        <small id="error_wallet_group_name" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label for="new_wallet_group_icon">Icon</label>
                        @include('components.icon-picker', ['name' => 'icon', 'value' => 'fa fa-wallet', 'id' => 'new_wallet_group'])
                        <small id="error_wallet_group_icon" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label for="new_wallet_group_description">Deskripsi</label>
                        <textarea class="form-control" id="new_wallet_group_description" name="description" rows="3"></textarea>
                        <small id="error_wallet_group_description" class="text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveWalletGroup">
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
    $('#formAddWalletGroup').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.text-danger').text('');
        $('#btnSaveWalletGroup').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '{{ route("wallet-groups.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Add new option to select
                    const newOption = new Option(response.data.name, response.data.id, true, true);
                    $('#wallet_group_id').append(newOption).trigger('change');

                    // Close modal and reset form
                    $('#modalAddWalletGroup').modal('hide');
                    $('#formAddWalletGroup')[0].reset();

                    // Show success notification
                    $.notify({
                        icon: 'fa fa-check',
                        message: 'Grup dompet berhasil ditambahkan!'
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
                        $(`#error_wallet_group_${field}`).text(messages[0]);
                    }
                } else {
                    $.notify({
                        icon: 'fa fa-times',
                        message: 'Gagal menambahkan grup dompet!'
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
                $('#btnSaveWalletGroup').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
            }
        });
    });

    // Reset form when modal is closed
    $('#modalAddWalletGroup').on('hidden.bs.modal', function() {
        $('#formAddWalletGroup')[0].reset();
        $('.text-danger').text('');
    });
});
</script>
@endpush
