@extends('layouts.app')

@section('title', 'Manajemen User - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Manajemen User</h4>
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
            <a href="#">Manajemen User</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Daftar User</h4>
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah User
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
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Dibuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $userItem)
                            <tr>
                                <td>{{ $userItem->name }}</td>
                                <td>{{ $userItem->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $userItem->hasRole('admin') ? 'primary' : ($userItem->hasRole('manager') ? 'info' : 'secondary') }}">
                                        {{ $userItem->getRoleNames()->first() ?? 'No Role' }}
                                    </span>
                                </td>
                                <td>@shortIndonesianDate($userItem->created_at)</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('users.edit', $userItem) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if($userItem->id !== auth()->user()->id)
                                        <form method="POST" action="{{ route('users.destroy', $userItem) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
