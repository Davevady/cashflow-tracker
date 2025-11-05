@extends('layouts.app')

@section('title', 'Anggota Keluarga - CashFlow Tracker')

@section('content')
<div class="page-header">
    <h4 class="page-title">Anggota Keluarga</h4>
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
            <a href="#">Anggota Keluarga</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Semua Anggota Keluarga</h4>
                    <a href="{{ route('members.create') }}" class="btn btn-primary btn-round ml-auto">
                        <i class="fa fa-plus"></i> Tambah Anggota
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
                                <th width="5%">Icon</th>
                                <th width="20%">Nama Anggota</th>
                                <th width="30%">Deskripsi</th>
                                <th width="12%">Jumlah Transaksi</th>
                                <th width="15%">Dibuat</th>
                                <th width="13%" class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members as $index => $member)
                                <tr>
                                    <td>{{ $members->firstItem() + $index }}</td>
                                    <td>
                                        @include('components.icon-render', ['icon' => $member->icon])
                                    </td>
                                    <td>
                                        <strong>{{ $member->name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $member->description ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $member->transactions_count ?? 0 }} transaksi</span>
                                    </td>
                                    <td>
                                        <small class="datetime-text" data-datetime="{{ $member->created_at }}">{{ $member->created_at }}</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('members.edit', $member) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('members.destroy', $member) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted">Belum ada anggota keluarga</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($members->hasPages())
                    <div class="mt-3">
                        {{ $members->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
