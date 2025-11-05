@extends('layouts.app')

@section('title', 'Kelola Permission Role - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Kelola Permission Role: {{ ucfirst($role->name) }}</h4>
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
            <a href="{{ route('roles.index') }}">Role</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Kelola Permission</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Assign Permission ke Role: <strong class="text-primary">{{ ucfirst($role->name) }}</strong></h4>
            </div>
            <form action="{{ route('roles.permissions.update', $role) }}" method="POST" id="permissionForm">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Unassigned Permissions -->
                        <div class="col-md-5">
                            <div class="card" style="min-height: 400px;">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fa fa-list"></i> Available Permissions
                                        <span class="badge badge-light float-right" id="unassignedCount">0</span>
                                    </h5>
                                </div>
                                <div class="card-body p-2" style="max-height: 500px; overflow-y: auto;">
                                    <div id="unassignedList" class="permission-list">
                                        @foreach($permissions as $permission)
                                            @if(!in_array($permission->id, $rolePermissions))
                                                <div class="permission-item" data-id="{{ $permission->id }}" data-name="{{ $permission->name }}">
                                                    <i class="fa fa-plus-circle text-success"></i> {{ $permission->name }}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Center Arrows -->
                        <div class="col-md-2 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <button type="button" class="btn btn-primary btn-block mb-3" id="assignAll">
                                    <i class="fa fa-angle-double-right"></i><br>
                                    <small>Assign All</small>
                                </button>
                                <button type="button" class="btn btn-danger btn-block" id="unassignAll">
                                    <i class="fa fa-angle-double-left"></i><br>
                                    <small>Remove All</small>
                                </button>
                            </div>
                        </div>

                        <!-- Assigned Permissions -->
                        <div class="col-md-5">
                            <div class="card" style="min-height: 400px;">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fa fa-check-circle"></i> Assigned Permissions
                                        <span class="badge badge-light float-right" id="assignedCount">0</span>
                                    </h5>
                                </div>
                                <div class="card-body p-2" style="max-height: 500px; overflow-y: auto;">
                                    <div id="assignedList" class="permission-list">
                                        @foreach($permissions as $permission)
                                            @if(in_array($permission->id, $rolePermissions))
                                                <div class="permission-item assigned" data-id="{{ $permission->id }}" data-name="{{ $permission->name }}">
                                                    <i class="fa fa-minus-circle text-danger"></i> {{ $permission->name }}
                                                    <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Cara penggunaan:</strong> Klik permission di sisi kiri untuk assign ke role, atau klik permission di sisi kanan untuk remove dari role.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-action">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan Permission
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-danger">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.permission-list {
    min-height: 350px;
}

.permission-item {
    padding: 12px 15px;
    margin-bottom: 8px;
    background-color: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
}

.permission-item:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    transform: translateX(3px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.permission-item.assigned {
    background-color: #d4edda;
    border-color: #28a745;
}

.permission-item.assigned:hover {
    background-color: #c3e6cb;
    border-color: #1e7e34;
}

.permission-item.active {
    background-color: #cce5ff;
    border-color: #004085;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.permission-item i {
    margin-right: 8px;
    font-size: 14px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Update counts
    function updateCounts() {
        const unassignedCount = $('#unassignedList .permission-item').length;
        const assignedCount = $('#assignedList .permission-item').length;

        $('#unassignedCount').text(unassignedCount);
        $('#assignedCount').text(assignedCount);
    }

    // Handle click on unassigned permission (assign to role)
    $(document).on('click', '#unassignedList .permission-item', function() {
        const $item = $(this);
        const permissionId = $item.data('id');
        const permissionName = $item.data('name');

        // Add active effect
        $item.addClass('active');

        setTimeout(function() {
            // Create new assigned item
            const $newItem = $('<div>', {
                'class': 'permission-item assigned',
                'data-id': permissionId,
                'data-name': permissionName,
                'html': '<i class="fa fa-minus-circle text-danger"></i> ' + permissionName +
                       '<input type="hidden" name="permissions[]" value="' + permissionId + '">'
            });

            // Move to assigned list
            $('#assignedList').append($newItem);
            $item.remove();

            updateCounts();
        }, 150);
    });

    // Handle click on assigned permission (unassign from role)
    $(document).on('click', '#assignedList .permission-item', function() {
        const $item = $(this);
        const permissionId = $item.data('id');
        const permissionName = $item.data('name');

        // Add active effect
        $item.addClass('active');

        setTimeout(function() {
            // Create new unassigned item
            const $newItem = $('<div>', {
                'class': 'permission-item',
                'data-id': permissionId,
                'data-name': permissionName,
                'html': '<i class="fa fa-plus-circle text-success"></i> ' + permissionName
            });

            // Move to unassigned list
            $('#unassignedList').append($newItem);
            $item.remove();

            updateCounts();
        }, 150);
    });

    // Assign all permissions
    $('#assignAll').on('click', function() {
        $('#unassignedList .permission-item').each(function() {
            const $item = $(this);
            const permissionId = $item.data('id');
            const permissionName = $item.data('name');

            const $newItem = $('<div>', {
                'class': 'permission-item assigned',
                'data-id': permissionId,
                'data-name': permissionName,
                'html': '<i class="fa fa-minus-circle text-danger"></i> ' + permissionName +
                       '<input type="hidden" name="permissions[]" value="' + permissionId + '">'
            });

            $('#assignedList').append($newItem);
        });

        $('#unassignedList').empty();
        updateCounts();
    });

    // Unassign all permissions
    $('#unassignAll').on('click', function() {
        $('#assignedList .permission-item').each(function() {
            const $item = $(this);
            const permissionId = $item.data('id');
            const permissionName = $item.data('name');

            const $newItem = $('<div>', {
                'class': 'permission-item',
                'data-id': permissionId,
                'data-name': permissionName,
                'html': '<i class="fa fa-plus-circle text-success"></i> ' + permissionName
            });

            $('#unassignedList').append($newItem);
        });

        $('#assignedList').empty();
        updateCounts();
    });

    // Initialize counts
    updateCounts();
});
</script>
@endpush
