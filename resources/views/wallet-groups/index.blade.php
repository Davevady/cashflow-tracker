@extends('layouts.app')

@section('title', 'Grup Dompet')

@section('content')
<div class="page-header">
    <h4 class="page-title">Grup Dompet</h4>
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
            <a href="#">Grup Dompet</a>
        </li>
    </ul>
    </div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Semua Grup Dompet</h4>
                    <a href="{{ route('wallet-groups.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah Grup
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
                                <th>Icon</th>
                                <th>Deskripsi</th>
                                <th style="width:150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr>
                                    <td>{{ $group->name }}</td>
                                    <td>@include('components.icon-render', ['icon' => $group->icon])</td>
                                    <td>{{ $group->description }}</td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('wallet-groups.edit', $group) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('wallet-groups.destroy', $group) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus grup ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Hapus"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($groups->hasPages())
                    <div class="mt-3">
                        {{ $groups->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


