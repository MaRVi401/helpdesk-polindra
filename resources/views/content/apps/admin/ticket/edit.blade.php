@extends('layouts.contentNavbarLayout')

@section('title', 'Detail & Edit Tiket ' . $tiket->no_tiket)

@section('content')
<div class="row">
    <!-- Kolom Kiri -->
    <div class="col-lg-8">
        
        <!-- Detail Tiket -->
        <div class="card mb-4">
            <h5 class="card-header">Detail Tiket #{{ $tiket->no_tiket }}</h5>
            <div class="card-body">
                <p><strong>Layanan:</strong> {{ $tiket->layanan->nama ?? '-' }} 
                    (Unit: {{ $tiket->layanan->unit->nama_unit ?? '-' }})
                </p>
                <p><strong>Pemohon:</strong> 
                    {{ $tiket->pemohon->name ?? '-' }}
                    (NIM: {{ $tiket->pemohon->mahasiswa->nim ?? '-' }})
                </p>

                <hr>
                <h6>Deskripsi:</h6>
                <p>{{ $tiket->deskripsi }}</p>

                {{-- DETAIL LAYANAN SPESIFIK --}}
                @if($detailLayanan)
                    <hr>
                    <h6>Detail Layanan Spesifik:</h6>

                    {{-- Surat Keterangan Aktif --}}
                    @if(str_contains($tiket->layanan->nama, 'Surat Keterangan Aktif'))
                        <p><strong>Tujuan Surat:</strong> {{ $detailLayanan->tujuan_surat ?? '-' }}</p>
                        <p><strong>Kebutuhan Semester:</strong> {{ $detailLayanan->kebutuhan_semester ?? '-' }}</p>
                    @endif

                    {{-- Reset Akun --}}
                    @if(str_contains($tiket->layanan->nama, 'Reset Akun'))
                        <p><strong>Jenis Akun:</strong> {{ $detailLayanan->jenis_akun ?? '-' }}</p>
                    @endif

                    {{-- Ubah Data Mahasiswa --}}
                    @if(str_contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                        <p><strong>Data yang Diubah:</strong> {{ $detailLayanan->data_diubah ?? '-' }}</p>
                    @endif

                    {{-- Request Publikasi --}}
                    @if(str_contains($tiket->layanan->nama, 'Request Publikasi'))
                        <p><strong>Judul Publikasi:</strong> {{ $detailLayanan->judul ?? '-' }}</p>
                        <p><strong>Deskripsi Konten:</strong> {{ $detailLayanan->deskripsi_konten ?? '-' }}</p>

                        @if($detailLayanan->gambar)
                            <p><strong>Gambar Publikasi:</strong></p>
                            <img src="{{ asset('storage/' . $detailLayanan->gambar) }}"
                                 class="img-fluid rounded"
                                 style="max-width: 300px;">
                        @endif
                    @endif
                @endif

            </div>
        </div>

        <!-- Riwayat Status -->
        <div class="card mb-4">
            <h5 class="card-header">Riwayat Status</h5>
            <div class="card-body">
                <ul class="timeline">
                    @foreach($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-info"></span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">{{ str_replace('_', ' ', $riwayat->status) }}</h6>
                                    <small class="text-muted">{{ $riwayat->created_at->format('d M Y H:i') }}</small>
                                </div>
                                <p>Oleh: <strong>{{ $riwayat->user->name ?? 'Sistem' }}</strong>
                                   ({{ $riwayat->user->role ?? 'Sistem' }})
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Komentar -->
        <div class="card mb-4">
            <h5 class="card-header">Komentar</h5>
            <div class="card-body">
                @foreach($tiket->komentar->sortBy('created_at') as $komentar)
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-secondary">
                                    {{ substr($komentar->pengirim->name ?? 'S', 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between">
                                    <h6>{{ $komentar->pengirim->name ?? 'Sistem' }}
                                        <span class="badge bg-label-info">{{ $komentar->pengirim->role ?? 'Sistem' }}</span>
                                    </h6>
                                    <small class="text-muted">{{ $komentar->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0">{{ $komentar->komentar }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Kolom Kanan -->
    <div class="col-lg-4">
        <div class="card">
            <h5 class="card-header">Update Tiket</h5>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('ticket.update', $tiket) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Ubah Status</label>
                        <select class="form-select" name="status">
                            <option value="">Jangan Ubah Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" 
                                    {{ $status == $statusSekarang ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $status) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Status saat ini: <strong>{{ str_replace('_', ' ', $statusSekarang) }}</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tambah Komentar (Opsional)</label>
                        <textarea class="form-control" name="komentar" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Update Tiket</button>
                </form>

                <hr>

                <form action="{{ route('ticket.destroy', $tiket) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        Hapus Tiket Permanen
                    </button>
                </form>

            </div>
        </div>
    </div>

</div>
@endsection
