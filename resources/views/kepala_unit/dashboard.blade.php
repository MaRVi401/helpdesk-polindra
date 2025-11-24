@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Kepala Unit')

@section('content')
<div class="row">
    
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4 class="text-white mb-0">Unit: {{ $unitDipimpin->nama_unit ?? 'Tidak Ada Unit' }}</h4>
                <small>Kepala Unit: {{ Auth::user()->name }}</small>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Tiket Masuk (Unit {{ $unitDipimpin->nama_unit ?? '-' }})</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No Tiket</th>
                            <th>Judul</th>
                            <th>Pelapor</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tikets as $tiket)
                        <tr>
                            <td>{{ $tiket->no_tiket }}</td>
                            <td>{{ Str::limit($tiket->judul ?? 'Tiket Tanpa Judul', 30) }}</td>
                            <td>{{ $tiket->mahasiswa->user->name ?? 'N/A' }}</td>
                            <td>{{ $tiket->layanan->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $status = $tiket->statusTerbaru->status ?? 'Diajukan_oleh_Pemohon';
                                    $badgeClass = match($status) {
                                        'Diselesaikan_oleh_PIC', 'Dinilai_Selesai_oleh_Pemohon' => 'bg-label-success',
                                        'Ditangani_oleh_PIC' => 'bg-label-warning',
                                        'Diajukan_oleh_Pemohon' => 'bg-label-primary',
                                        default => 'bg-label-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $status) }}</span>
                            </td>
                            <td>
                                <a href="{{ url('kepala-unit/tiket/' . $tiket->id) }}" class="btn btn-sm btn-icon btn-primary">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada tiket untuk unit ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Kelola Admin Unit / PIC Layanan</h5>
                <small class="text-muted">Atur staff yang bertanggung jawab untuk setiap layanan di unit ini.</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Layanan</th>
                                <th>Penanggung Jawab (PIC) Saat Ini</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($layanans as $layanan)
                            <tr>
                                <td><strong>{{ $layanan->nama }}</strong></td>
                                <td>
                                    @forelse($layanan->penanggungJawab as $pic)
                                        <span class="badge bg-label-info mb-1">{{ $pic->user->name ?? $pic->nik }}</span>
                                    @empty
                                        <span class="text-danger"><i class="bx bx-error"></i> Belum ada PIC</span>
                                    @endforelse
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalKelolaPIC{{ $layanan->id }}">
                                        Atur PIC
                                    </button>

                                    <div class="modal fade" id="modalKelolaPIC{{ $layanan->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form class="modal-content" action="{{ route('kepala-unit.update-pic', $layanan->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Atur PIC: {{ $layanan->nama }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Pilih Staff</label>
                                                        <select name="staff_ids[]" class="form-select select2" multiple>
                                                            @foreach($staffUnit as $staff)
                                                                <option value="{{ $staff->id }}" 
                                                                    {{ $layanan->penanggungJawab->contains($staff->id) ? 'selected' : '' }}>
                                                                    {{ $staff->user->name ?? $staff->nik }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted">Gunakan Ctrl+Click untuk memilih lebih dari satu.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3">Belum ada layanan terdaftar di unit ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection