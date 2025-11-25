@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Tiket #' . $tiket->no_tiket)

@section('content')
<style>
    /* --- CSS Status Badge --- */
    .status-badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; color: white; text-transform: capitalize; }
    .status-diajukan_oleh_pemohon { background-color: #a0aec0; color: #2d3748; } /* Abu-abu */
    .status-ditangani_oleh_pic, .status-diselesaikan_oleh_pic { background-color: #f6ad55; color: white; } /* Kuning */
    .status-dinilai_belum_selesai_oleh_pemohon, .status-pemohon_bermasalah, .status-ditolak { background-color: #f56565; color: white; } /* Merah */
    .status-dinilai_selesai_oleh_kepala, .status-dinilai_selesai_oleh_pemohon { background-color: #48bb78; color: white; } /* Hijau */

    /* --- CSS Prioritas --- */
    .prioritas-rendah { background-color: #a0aec0; color: white; }
    .prioritas-sedang { background-color: #48bb78; color: white; }
    .prioritas-tinggi { background-color: #f56565; color: white; }
    
    /* --- Timeline Style --- */
    .timeline { position: relative; padding-left: 1rem; }
    .timeline-item { position: relative; padding-left: 2rem; padding-bottom: 1.5rem; border-left: 2px solid #e2e8f0; }
    .timeline-item:last-child { border-left: 0; padding-bottom: 0; }
    .timeline-point { position: absolute; left: -6px; top: 0; width: 12px; height: 12px; border-radius: 50%; background-color: #cbd5e0; border: 2px solid #fff; }
    
    /* --- Chat Bubble Style --- */
    .chat-history { list-style: none; padding: 0; margin: 0; }
    .chat-message { display: flex; margin-bottom: 1rem; }
    .chat-message-right { justify-content: flex-end; }
    .chat-message-content { background-color: #f1f0f0; padding: 0.75rem 1rem; border-radius: 0.375rem; max-width: 80%; }
    .chat-message-right .chat-message-content { background-color: #e7f3ff; color: #333; }

    .timeline { border-left: 3px solid #e2e8f0; margin-left: 10px; padding-left: 20px; }
    .timeline-item { position: relative; margin-bottom: 1.5rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot { position: absolute; left: -31px; top: 5px; width: 15px; height: 15px; border-radius: 50%; background-color: #4299e1; }
    .timeline-time { font-size: 0.85rem; color: #718096; margin-bottom: 0.25rem; }
    .timeline-title { font-weight: 600; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .timeline-body p { margin: 0; font-size: 0.95rem; }
    .timeline-body-comment { background-color: #f7fafc; border: 1px solid #e2e8f0; padding: 1rem; border-radius: 6px; }
    
    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; color: white; text-transform: capitalize; }
    
    .status-diajukan_oleh_pemohon { background-color: #a0aec0; } 
    .status-ditangani_oleh_pic, .status-diselesaikan_oleh_pic { background-color: #f6ad55; } 
    .status-dinilai_belum_selesai_oleh_pemohon, .status-pemohon_bermasalah { background-color: #f56565; } 
    .status-dinilai_selesai_oleh_kepala, .status-dinilai_selesai_oleh_pemohon { background-color: #48bb78; } 

    .dot-super_admin { background-color: #e53e3e !important; }
    .body-super_admin { background-color: #fef2f2 !important; border-color: #fbd7d7 !important; }
    .dot-mahasiswa { background-color: #4299e1 !important; }
    .body-mahasiswa { background-color: #ebf8ff !important; border-color: #bee3f8 !important; }
    .dot-kepala_unit { background-color: #805ad5 !important; }
    .body-kepala_unit { background-color: #faf5ff !important; border-color: #e9d8fd !important; }
    .dot-admin_unit { background-color: #38a169 !important; }
    .body-admin_unit { background-color: #f0fff4 !important; border-color: #c6f6d5 !important; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Monitoring /</span> Detail Tiket
    </h4>

    <div class="row">
        <!-- KOLOM KIRI: Form Aksi & Riwayat -->
        <div class="col-md-8">
            
            <!-- 1. KARTU AKSI: Update Status (Dengan Validasi Pemohon_Bermasalah) -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tindak Lanjut</h5>
                    <span class="badge bg-label-primary">{{ $tiket->layanan->unit->nama_unit ?? '-' }}</span>
                </div>
                <div class="card-body">
                    @php
                        $currentStatus = $tiket->statusTerbaru->status ?? '';
                        // VALIDASI VIEW: Menu hanya aktif jika status == 'Pemohon_Bermasalah'
                        $canEdit = ($currentStatus === 'Pemohon_Bermasalah');
                    @endphp

                    @if(!$canEdit)
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="bx bx-lock-alt me-2"></i>
                            <div>
                                <strong>Menu Terkunci:</strong> Anda hanya dapat mengubah status jika tiket saat ini berstatus <strong>"Pemohon Bermasalah"</strong>. Saat ini status adalah: <em>{{ str_replace('_', ' ', $currentStatus) }}</em>.
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('kepala-unit.monitoring.update', $tiket->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Validasi Status (Kepala Unit)</label>
                                <select name="status" class="form-select" {{ $canEdit ? '' : 'disabled' }}>
                                    <option value="" selected disabled>-- Pilih Keputusan --</option>
                                    <option value="Dinilai_Selesai_oleh_Kepala">✅ Setujui Selesai (Final)</option>
                                </select>
                                @if($canEdit)
                                    <div class="form-text text-primary">Silakan validasi tiket ini untuk penyelesaian akhir.</div>
                                @endif
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100" {{ $canEdit ? '' : 'disabled' }}>
                                    <i class="bx bx-check-circle me-1"></i> Simpan Keputusan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. KARTU KOMENTAR: Form Tambah Komentar & List Komentar -->
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Diskusi & Komentar</h5>
                </div>
                <div class="card-body pt-4">
                    <!-- Form Tambah Komentar (Selalu Aktif agar bisa diskusi) -->
                    <form action="{{ route('kepala-unit.monitoring.komentar', $tiket->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Balas / Tambah Catatan</label>
                            <textarea name="komentar" class="form-control" rows="2" placeholder="Tulis pesan untuk pemohon atau catatan internal..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bx bx-send me-1"></i> Kirim Komentar
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- List Riwayat Komentar -->
                    <div class="card">
                    <div class="card-header">Riwayat Komentar ({{ $tiket->komentar->count() }})</div>
                    <div class="card-body">
                        <div class="timeline">
                            @forelse($tiket->komentar as $komen)
                                @php
                                    $role = $komen->pengirim->role ?? 'unknown';
                                    $dotClass = 'dot-' . $role;
                                    $bodyClass = 'body-' . $role;
                                @endphp
                                <div class="timeline-item">
                                    <span class="timeline-dot {{ $dotClass }}"></span>
                                    <div class="timeline-time">{{ $komen->created_at->format('d M Y, H:i') }}</div>
                                    <div class="timeline-title">
                                        {{ $komen->pengirim->name }} (<strong style="text-transform: capitalize;">{{ str_replace('_', ' ', $role) }}</strong>)
                                    </div>
                                    <div class="timeline-body timeline-body-comment {{ $bodyClass }}">
                                        <p>{!! nl2br(e($komen->komentar)) !!}</p>
                                    </div>
                                </div>
                            @empty
                                <p>Belum ada komentar.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            <!-- 3. KARTU LOG STATUS (Terpisah di Bawah) -->
            <div class="card">
                    <div class="card-header">Riwayat Status</div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach ($tiket->riwayatStatus as $riwayat)
                                @php
                                    $dotColor = '#718096'; // Default Gray
                                    $statusLower = strtolower($riwayat->status);

                                    if (str_contains($statusLower, 'diajukan')) {
                                        $dotColor = '#a0aec0'; // Gray
                                    } elseif (str_contains($statusLower, 'ditangani') || str_contains($statusLower, 'diselesaikan')) {
                                        $dotColor = '#f6ad55'; // Orange
                                    } elseif ($riwayat->status == 'Dinilai_Belum_Selesai_oleh_Pemohon' || $riwayat->status == 'Pemohon_Bermasalah') {
                                        $dotColor = '#f56565'; // Red
                                    } elseif ($riwayat->status == 'Dinilai_Selesai_oleh_Kepala' || $riwayat->status == 'Dinilai_Selesai_oleh_Pemohon') {
                                        $dotColor = '#48bb78'; // Green
                                    }
                                @endphp
                                <div class="timeline-item">
                                    <span class="timeline-dot" style="background-color: {{ $dotColor }};"></span>
                                    <div class="timeline-time">{{ $riwayat->created_at->format('d M Y, H:i') }}</div>
                                    
                                    <div class="timeline-title">
                                        Status: 
                                        <span style="
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            background-color: {{ $dotColor }};
                                            color: white;
                                            padding: 4px 12px;
                                            border-radius: 12px;
                                            font-size: 0.75rem;
                                            font-weight: 700;
                                            line-height: 1.2;
                                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                            text-transform: capitalize;
                                        ">
                                            {{ str_replace('_', ' ', $riwayat->status) }}
                                        </span>
                                    </div>

                                    <div class="timeline-body">
                                        <p>Oleh: {{ $riwayat->user->name ?? 'Sistem' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

        <!-- KOLOM KANAN: Info Tiket (Sidebar) -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-label-secondary text-dark fw-bold">Informasi Tiket</div>
                <div class="card-body pt-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">No Tiket</dt>
                        <dd class="col-sm-8 text-end fw-bold">#{{ $tiket->no_tiket }}</dd>

                        <dt class="col-sm-4">Layanan</dt>
                        <dd class="col-sm-8 text-end">{{ $tiket->layanan->nama }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8 text-end">
                            @php
                                $st = $tiket->statusTerbaru->status ?? 'Baru';
                                $cls = 'status-' . strtolower($st);
                            @endphp
                            <span class="status-badge {{ $cls }}">{{ str_replace('_', ' ', $st) }}</span>
                        </dd>

                        <dt class="col-sm-4 mt-2">Prioritas</dt>
                        <dd class="col-sm-8 mt-2 text-end">
                            @php
                                // Prioritas diambil dari Layanan (Read-only di sini karena tidak bisa diedit)
                                $prioVal = $tiket->layanan->prioritas ?? 2;
                                $prioLabelShow = 'Normal';
                                $prioClassShow = 'bg-secondary';

                                if ($prioVal == 3) { 
                                    $prioLabelShow = 'Tinggi'; $prioClassShow = 'prioritas-tinggi'; 
                                } elseif ($prioVal == 2) { 
                                    $prioLabelShow = 'Sedang'; $prioClassShow = 'prioritas-sedang'; 
                                } elseif ($prioVal == 1) { 
                                    $prioLabelShow = 'Rendah'; $prioClassShow = 'prioritas-rendah'; 
                                }
                            @endphp
                            <span class="badge {{ $prioClassShow }}">{{ $prioLabelShow }}</span>
                        </dd>
                        
                        <hr class="my-3">
                        
                        <dt class="col-sm-12 mb-1">Deskripsi Awal:</dt>
                        <dd class="col-sm-12 bg-lighter p-2 rounded text-break">
                            {{ $tiket->deskripsi }}
                        </dd>
                    </dl>

                    <!-- Detail Data Spesifik -->
                    @if(isset($detailLayanan))
                        <hr>
                        <h6 class="fw-bold small">Data Tambahan</h6>
                        <ul class="ps-3 mb-0 small text-muted">
                            @if(\Illuminate\Support\Str::contains($tiket->layanan->nama, 'Surat Keterangan'))
                                <li>Keperluan: {{ $detailLayanan->keperluan }}</li>
                            @elseif(\Illuminate\Support\Str::contains($tiket->layanan->nama, 'Reset Akun'))
                                <li>Aplikasi: {{ $detailLayanan->aplikasi }}</li>
                            @elseif(\Illuminate\Support\Str::contains($tiket->layanan->nama, 'Ubah Data'))
                                <li>Nama Baru: {{ $detailLayanan->data_nama_lengkap }}</li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Info Pemohon -->
            <div class="card">
                <div class="card-header">Data Pemohon</div>
                <div class="card-body text-center">
                    <div class="avatar avatar-xl mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-info">
                            {{ strtoupper(substr($tiket->pemohon->name ?? 'U', 0, 2)) }}
                        </span>
                    </div>
                    <h5 class="mb-1">{{ $tiket->pemohon->name }}</h5>
                    <p class="text-muted mb-0">{{ $tiket->pemohon->email }}</p>
                    @if($tiket->pemohon->mahasiswa)
                        <hr>
                        <div class="text-start">
                            <small class="d-block"><strong>NIM:</strong> {{ $tiket->pemohon->mahasiswa->nim }}</small>
                            <small class="d-block"><strong>Prodi:</strong> {{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? '-' }}</small>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('kepala-unit.monitoring.index') }}" class="btn btn-outline-secondary w-100">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection