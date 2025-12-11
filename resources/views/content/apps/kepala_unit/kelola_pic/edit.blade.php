@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Layanan - ' . $layanan->nama)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Layanan / {{ $unit->nama_unit }} /</span> Edit Layanan
    </h4>

    <div class="row">
        <!-- KOLOM KIRI: DATA DASAR -->
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Data Dasar Layanan</h5>
                <div class="card-body">
                    <!-- Route ke KelolaPicController -->
                    <form action="{{ route('kepala-unit.pic.update', $layanan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="nama">Nama Layanan</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $layanan->nama) }}" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="unit">Unit Penanggung Jawab</label>
                            <input type="text" class="form-control" value="{{ $unit->nama_unit }}" disabled />
                            <div class="form-text">Unit tidak dapat diubah.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="prioritas">Prioritas</label>
                            <select class="form-select" id="prioritas" name="prioritas" required>
                                <option value="3" {{ old('prioritas', $layanan->prioritas) == 3 ? 'selected' : '' }}>Tinggi (High)</option>
                                <option value="2" {{ old('prioritas', $layanan->prioritas) == 2 ? 'selected' : '' }}>Sedang (Medium)</option>
                                <option value="1" {{ old('prioritas', $layanan->prioritas) == 1 ? 'selected' : '' }}>Rendah (Low)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="status_arsip">Status Layanan</label>
                            <select class="form-select" id="status_arsip" name="status_arsip" required>
                                <option value="0" {{ old('status_arsip', $layanan->status_arsip) == 0 ? 'selected' : '' }}>Aktif (Ditampilkan)</option>
                                <option value="1" {{ old('status_arsip', $layanan->status_arsip) == 1 ? 'selected' : '' }}>Diarsipkan (Disembunyikan)</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kepala-unit.pic.index') }}" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: KELOLA PIC -->
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header d-flex justify-content-between align-items-center">
                    Kelola Penanggung Jawab (PIC)
                    <span class="badge bg-primary">{{ $currentPICS->count() }} PIC</span>
                </h5>
                <div class="card-body">
                    
                    <!-- Form Tambah PIC -->
                    <form action="{{ route('kepala-unit.pic.update', $layanan->id) }}" method="POST" class="mb-4">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="pic_id_to_add" class="form-label">Tambah PIC Baru</label>
                            <div class="input-group">
                                <select class="form-select" id="pic_id_to_add" name="pic_id_to_add" required>
                                    <option value="">-- Pilih Staff --</option>
                                    @foreach($availableStaff as $staff)
                                        <option value="{{ $staff->id }}">
                                            {{ $staff->user->name ?? 'N/A' }} ({{ $staff->unit->nama_unit ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bx bx-plus"></i> Tambah
                                </button>
                            </div>
                            @if($availableStaff->isEmpty())
                                <div class="form-text text-warning">Semua staff sudah ditugaskan.</div>
                            @endif
                        </div>
                    </form>

                    <hr class="my-3">

                    <!-- List PIC Aktif -->
                    <h6 class="mb-3">PIC Aktif</h6>
                    <ul class="list-group list-group-flush">
                        @forelse($currentPICS as $pic)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            {{ strtoupper(substr($pic->user->name ?? 'U', 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $pic->user->name ?? 'Unknown' }}</span>
                                        <small class="text-muted">{{ $pic->unit->nama_unit ?? '-' }}</small>
                                    </div>
                                </div>
                                
                                <form action="{{ route('kepala-unit.pic.update', $layanan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus {{ $pic->user->name }} dari PIC?');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="pic_id_to_remove" value="{{ $pic->id }}">
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-muted text-center">Belum ada PIC yang ditugaskan.</li>
                        @endforelse
                    </ul>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection