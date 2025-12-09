@extends('layouts.contentNavbarLayout')

@section('title', 'Detail & Edit Tiket ' . $tiket->no_tiket)

@section('content')
<style>
    /* --- CSS Warna Status (Konsisten dengan View Index) --- */
    .status-badge { padding: 5px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 700; color: white; text-transform: capitalize; display: inline-block; min-width: 120px; text-align: center;}
    
    /* Mapping Warna */
    .status-diajukan_oleh_pemohon { background-color: #a0aec0; color: #fff; } /* Abu-abu */
    .status-ditangani_oleh_pic { background-color: #f6ad55; color: white; } /* Orange */
    .status-diselesaikan_oleh_pic { background-color: #f6ad55; color: white; } /* Orange */
    .status-dinilai_belum_selesai_oleh_pemohon { background-color: #e53e3e; color: white; } /* Merah */
    .status-pemohon_bermasalah { background-color: #e53e3e; color: white; } /* Merah */
    .status-ditolak { background-color: #e53e3e; color: white; } /* Merah */
    .status-dinilai_selesai_oleh_kepala { background-color: #38a169; color: white; } /* Hijau */
    .status-dinilai_selesai_oleh_pemohon { background-color: #38a169; color: white; } /* Hijau */
</style>

@php
    // Ambil status sekarang dan siapkan class CSS
    $currentStatus = $statusSekarang ?? 'Draft';
    $statusClass = 'status-' . strtolower(str_replace(' ', '_', $currentStatus));
@endphp

<div class="row">
    <div class="col-lg-8">

        <div class="card mb-4 shadow-sm">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bx bx-file me-2"></i>Detail Tiket: <strong>{{ $tiket->no_tiket }}</strong></span>
                {{-- Tampilkan status dengan warna CSS yang sudah disiapkan --}}
                <span class="status-badge {{ $statusClass }}">{{ str_replace('_', ' ', $currentStatus) }}</span>
            </h5>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>Layanan:</strong> {{ $tiket->layanan->nama ?? '-' }}</p>
                    <p>
                        <small class="text-muted">Unit Penanganan: 
                            <strong>{{ $tiket->layanan->unit->nama_unit ?? '-' }}</strong>
                        </small>
                    </p>
                </div>
                <div class="mb-4">
                    <p class="mb-1"><strong>Pemohon:</strong> {{ $tiket->pemohon->name ?? '-' }}</p>
                    <p><small class="text-muted">NIM: <strong>{{ $tiket->pemohon->mahasiswa->nim ?? '-' }}</strong></small></p>
                </div>

                <hr>
                <h6>Deskripsi Permohonan:</h6>
                <div class="p-3 border rounded bg-light mb-3">
                    <p class="mb-0 text-break">{{ $tiket->deskripsi }}</p>
                </div>

                {{-- DETAIL LAYANAN SPESIFIK --}}
                @if ($detailLayanan)
                    <hr>
                    <h6><i class="bx bx-info-circle me-1"></i>Detail Layanan Spesifik:</h6>
                    <div class="p-3 border rounded bg-label-secondary">
                        
                        {{-- Surat Keterangan Aktif --}}
                        @if (str_contains($tiket->layanan->nama, 'Surat Keterangan Aktif'))
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-nowrap">Keperluan Surat:</dt>
                                <dd class="col-sm-8 text-break">{{ $detailLayanan->keperluan ?? '-' }}</dd>

                                @if ($detailLayanan->keperluan_lainnya)
                                    <dt class="col-sm-4 text-nowrap text-muted">Keperluan Lainnya:</dt>
                                    <dd class="col-sm-8 text-muted fst-italic text-break">
                                        {{ $detailLayanan->keperluan_lainnya }}</dd>
                                @endif

                                <dt class="col-sm-4 text-nowrap">Tahun Ajaran:</dt>
                                <dd class="col-sm-8">{{ $detailLayanan->tahun_ajaran ?? '-' }}</dd>

                                <dt class="col-sm-4 text-nowrap">Semester:</dt>
                                <dd class="col-sm-8">{{ $detailLayanan->semester ?? '-' }}</dd>
                            </dl>
                        @endif

                        {{-- Reset Akun (Dirapikan menggunakan dl.row) --}}
                        @if (str_contains($tiket->layanan->nama, 'Reset Akun'))
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-nowrap">Aplikasi Direset:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-label-info">{{ strtoupper($detailLayanan->aplikasi ?? '-') }}</span>
                                </dd>
                                
                                <dt class="col-sm-4 text-nowrap">Deskripsi Masalah:</dt>
                                <dd class="col-sm-8 text-break">{{ $detailLayanan->deskripsi ?? '-' }}</dd>
                            </dl>
                        @endif

                        {{-- Ubah Data Mahasiswa (Dirapikan menggunakan dl.row) --}}
                        @if (str_contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-nowrap">Nama Lengkap (Baru):</dt>
                                <dd class="col-sm-8 text-break">{{ $detailLayanan->data_nama_lengkap ?? '-' }}</dd>

                                <dt class="col-sm-4 text-nowrap">Tempat Lahir (Baru):</dt>
                                <dd class="col-sm-8">{{ $detailLayanan->data_tmp_lahir ?? '-' }}</dd>

                                <dt class="col-sm-4 text-nowrap">Tanggal Lahir (Baru):</dt>
                                <dd class="col-sm-8">{{ $detailLayanan->data_tgl_lhr ?? '-' }}</dd>
                            </dl>
                        @endif

                        {{-- Request Publikasi (Dirapikan menggunakan dl.row) --}}
                        @if (str_contains($tiket->layanan->nama, 'Request Publikasi'))
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-nowrap">Judul Publikasi:</dt>
                                <dd class="col-sm-8 text-break">{{ $detailLayanan->judul ?? '-' }}</dd>

                                <dt class="col-sm-4 text-nowrap">Kategori:</dt>
                                <dd class="col-sm-8">{{ $detailLayanan->kategori ?? '-' }}</dd>

                                <dt class="col-sm-4 text-nowrap">Konten:</dt>
                                <dd class="col-sm-8 text-break">{{ $detailLayanan->konten ?? '-' }}</dd>
                            </dl>
                            
                            @if ($detailLayanan->gambar)
                                <h6 class="mt-3">Gambar Publikasi:</h6>
                                <img src="{{ asset('storage/' . $detailLayanan->gambar) }}"
                                    class="img-fluid rounded border p-1" style="max-width: 300px;">
                            @endif
                        @endif
                        
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <h5 class="card-header"><i class="bx bx-time-five me-2"></i>Riwayat Status</h5>
            <div class="card-body">
                <ul class="timeline">
                    @forelse ($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                        @php
                            $historyStatus = $riwayat->status ?? 'Draft';
                            $historyClass = 'status-' . strtolower(str_replace(' ', '_', $historyStatus));
                        @endphp
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-info"></span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    {{-- Tampilkan riwayat status dengan warna CSS yang sudah disiapkan --}}
                                    <span class="status-badge {{ $historyClass }}">{{ str_replace('_', ' ', $historyStatus) }}</span>
                                    <small class="text-muted">{{ $riwayat->created_at->format('d M Y H:i') }}</small>
                                </div>
                                <p class="mb-0">
                                    Oleh: <strong>{{ $riwayat->user->name ?? 'Sistem' }}</strong>
                                    <span class="badge bg-label-secondary">{{ $riwayat->user->role ?? 'Sistem' }}</span>
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="text-muted">Belum ada riwayat status.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <h5 class="card-header"><i class="bx bx-message-square-detail me-2"></i>Diskusi Komentar</h5>
            <div class="card-body">
                @forelse ($tiket->komentar->sortBy('created_at') as $komentar)
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-sm">
                                <span
                                    class="avatar-initial rounded-circle {{ ($komentar->pengirim->role ?? 'Sistem') == 'mahasiswa' ? 'bg-label-success' : 'bg-label-primary' }}">
                                    {{ substr($komentar->pengirim->name ?? 'S', 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6>
                                        {{ $komentar->pengirim->name ?? 'Sistem' }}
                                        <span
                                            class="badge bg-label-info">{{ $komentar->pengirim->role ?? 'Sistem' }}</span>
                                    </h6>
                                    <small class="text-muted">{{ $komentar->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 text-break">{{ $komentar->komentar }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light text-center">Belum ada komentar dalam tiket ini.</div>
                @endforelse
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4 shadow-sm">
            <h5 class="card-header"><i class="bx bx-check-shield me-2"></i>Update Status</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('ticket.update', $tiket) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Ubah Status</label>
                        <select class="form-select" name="status" required>
                            <option value="" disabled>-- Pilih Status Baru --</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}"
                                    {{ $status == $currentStatus ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $status) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Status saat ini:
                            <strong>{{ str_replace('_', ' ', $currentStatus) }}</strong></small>
                    </div>

                    <button type="submit" name="action" value="update_status" class="btn btn-primary w-100">Simpan Status</button>
                </form>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <h5 class="card-header"><i class="bx bx-comment-add me-2"></i>Tambah Komentar</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('ticket.update', $tiket) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Komentar/Tanggapan</label>
                        <textarea class="form-control" name="komentar" rows="3" required></textarea>
                    </div>

                    <button type="submit" name="action" value="add_comment" class="btn btn-secondary w-100">Kirim Komentar</button>
                </form>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <h5 class="card-header text-danger"><i class="bx bx-trash me-2"></i>Aksi Berbahaya</h5>
            <div class="card-body">
                <form action="{{ route('ticket.destroy', $tiket) }}" method="POST"
                    onsubmit="return confirm('❗ PERINGATAN: Apakah Anda yakin ingin menghapus tiket ini secara PERMANEN? Aksi ini tidak dapat dibatalkan.');">
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