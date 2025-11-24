@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola Layanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Layanan /</span> {{ $targetUnit->nama_unit }}</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Layanan</h5>
            <!-- Button Tambah Layanan (Modal Trigger) -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus me-1"></i> Tambah Layanan
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Layanan</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($layanans as $layanan)
                    <tr>
                        <td>{{ $layanan->nama }}</td>
                        <td><span class="badge bg-label-secondary">{{ $layanan->prioritas }}</span></td>
                        <td>
                            @if($layanan->status_arsip)
                                <span class="badge bg-label-danger">Arsip</span>
                            @else
                                <span class="badge bg-label-success">Aktif</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $layanan->id }}">
                                <i class="bx bx-edit"></i>
                            </button>
                            
                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal{{ $layanan->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form class="modal-content" action="{{ route('kepala-unit.service.unit.update', ['slug' => $slug, 'layanan' => $layanan->id]) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Layanan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Layanan</label>
                                                <input type="text" name="nama" class="form-control" value="{{ $layanan->nama }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Prioritas</label>
                                                <select name="prioritas" class="form-select">
                                                    <option value="1" {{ $layanan->prioritas == 1 ? 'selected' : '' }}>1 (Tinggi)</option>
                                                    <option value="2" {{ $layanan->prioritas == 2 ? 'selected' : '' }}>2 (Sedang)</option>
                                                    <option value="3" {{ $layanan->prioritas == 3 ? 'selected' : '' }}>3 (Rendah)</option>
                                                </select>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $layanan->id }}" {{ !$layanan->status_arsip ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active{{ $layanan->id }}">Aktif / Tampilkan</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Belum ada layanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ route('kepala-unit.service.unit.store', $slug) }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Layanan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Layanan</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-select">
                        <option value="1">1 (Tinggi)</option>
                        <option value="2" selected>2 (Sedang)</option>
                        <option value="3">3 (Rendah)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection