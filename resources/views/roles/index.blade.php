@extends('layouts.app')

@section('title', 'Kelola Role - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Kelola Role</h4>
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
            <a href="#">Role</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Daftar Role</h4>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah Role
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
                                <th width="20%">Nama Role</th>
                                <th width="15%">Guard Name</th>
                                <th width="10%" class="text-center">Jumlah Permission</th>
                                <th width="10%" class="text-center">Jumlah User</th>
                                <th width="15%">Dibuat</th>
                                <th width="15%" class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $index => $role)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong class="text-primary">{{ ucfirst($role->name) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $role->guard_name }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $role->permissions_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $role->users_count }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">@shortIndonesianDate($role->created_at)</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('roles.permissions', $role) }}"
                                               class="btn btn-sm btn-info"
                                               title="Kelola Permission">
                                                <i class="fa fa-key"></i>
                                            </a>
                                            <a href="{{ route('roles.edit', $role) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit Role">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $role->id }}"
                                                    data-name="{{ $role->name }}"
                                                    data-toggle="modal"
                                                    data-target="#deleteModal"
                                                    title="Hapus Role">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada role</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus role <strong id="roleName"></strong>?</p>
                    <p class="text-danger"><small>Role yang masih memiliki user tidak dapat dihapus.</small></p>
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
        $('#deleteForm').attr('action', '/roles/' + id);
        $('#roleName').text(name);
    });
});
</script>
@endpush
