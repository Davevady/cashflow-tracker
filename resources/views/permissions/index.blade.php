@extends('layouts.app')

@section('title', 'Kelola Permission - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Kelola Permission</h4>
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
            <a href="#">Permission</a>
        </li>
    </ul>
</div>

<div class="row">
    <!-- Permissions List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Permission</h4>
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

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">Nama Permission</th>
                                <th width="15%">Guard Name</th>
                                <th width="10%" class="text-center">Digunakan Role</th>
                                <th width="20%">Dibuat</th>
                                <th width="15%" class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $index => $permission)
                                <tr>
                                    <td>{{ $permissions->firstItem() + $index }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $permission->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $permission->guard_name }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $permission->roles()->count() }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">@shortIndonesianDate($permission->created_at)</small>
                                    </td>
                                    <td class="text-right">
                                        <button type="button"
                                                class="btn btn-sm btn-danger btn-delete"
                                                data-id="{{ $permission->id }}"
                                                data-name="{{ $permission->name }}"
                                                data-toggle="modal"
                                                data-target="#deleteModal">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada permission</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create New Permission -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tambah Permission Baru</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.create') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Permission <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="contoh: manage-users"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Gunakan format: manage-users, create-transactions, dll.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="guard_name">Guard Name</label>
                        <input type="text"
                               class="form-control"
                               id="guard_name"
                               name="guard_name"
                               value="web"
                               readonly>
                        <small class="form-text text-muted">
                            Default: web
                        </small>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fa fa-plus"></i> Tambah Permission
                    </button>
                </form>
            </div>
        </div>

        <div class="card card-info mt-3">
            <div class="card-header">
                <h4 class="card-title">Informasi</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="fa fa-info-circle"></i> Permission adalah hak akses yang dapat diberikan ke role.
                </p>
                <p class="text-muted">
                    Setelah membuat permission, Anda dapat assign permission ke role di halaman <a href="{{ route('roles.index') }}">Kelola Role</a>.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Permission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus permission <strong id="permissionName"></strong>?</p>
                    <p class="text-danger"><small>Permission yang masih digunakan oleh role tidak dapat dihapus.</small></p>
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
    // Handle Delete button click
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        // Set form action
        $('#deleteForm').attr('action', '/permissions/' + id);
        $('#permissionName').text(name);
    });
});
</script>
@endpush
