@extends('layouts.app')
@section('title', 'Daftar Tiket Layanan')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Layanan /</span> Daftar Tiket Saya</h4>
    <div class="card">
        <div class="card-header flex-column flex-md-row">
            <div class="dt-action-buttons text-end pt-3 pt-md-0">
                <div class="dt-buttons">
                    <a href="{{ route('mahasiswa.tiket.create') }}" class="btn btn-primary">
                        <span><i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Buat Tiket Baru</span></span>
                    </a>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Tiket</th>
                        <th>Layanan</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($tikets as $tiket)
                        <tr>
                            <td><a href="{{ route('mahasiswa.tiket.show', $tiket->id) }}"><strong>{{ $tiket->no_tiket }}</strong></a></td>
                            <td>{{ $tiket->layanan->nama }}</td>
                            <td>
                                @if($tiket->status == 'Diajukan') <span class="badge bg-label-primary me-1">{{ $tiket->status }}</span>
                                @elseif($tiket->status == 'Diproses') <span class="badge bg-label-info me-1">{{ $tiket->status }}</span>
                                @elseif($tiket->status == 'Selesai') <span class="badge bg-label-success me-1">{{ $tiket->status }}</span>
                                @else <span class="badge bg-label-danger me-1">{{ $tiket->status }}</span> @endif
                            </td>
                            <td>{{ $tiket->created_at->format('d M Y, H:i') }}</td>
                            <td><a href="{{ route('mahasiswa.tiket.show', $tiket->id) }}" class="btn btn-sm btn-info">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Anda belum memiliki tiket layanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $tikets->links() }}</div>
    </div>
</div>
@endsection