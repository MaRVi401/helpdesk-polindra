@extends('layouts/layoutMaster')

@section('title', 'Detail Tiket')

@section('vendor-style')
<style>
    /* --- LAYOUT & CARD STYLES (Mirip Admin) --- */
    .ticket-layout { display: flex; flex-wrap: wrap; gap: 1.5rem; }
    .ticket-main { flex: 2; min-width: 300px; }
    .ticket-sidebar { flex: 1; min-width: 300px; }

    .card-header-custom { 
        padding: 1rem 1.5rem; 
        background-color: #f8f9fa; 
        border-bottom: 1px solid #e2e8f0; 
        font-weight: 600; 
        color: #4a5568; 
        display: flex;
        align-items: center;
    }
    .card-body-custom { padding: 1.5rem; }
    
    /* --- DEFINITION LIST (SIDEBAR) --- */
    .info-grid { display: grid; grid-template-columns: 130px 1fr; gap: 0.75rem; font-size: 0.9rem; margin-bottom: 0; }
    .info-grid dt { font-weight: 600; color: #718096; }
    .info-grid dd { margin: 0; color: #2d3748; font-weight: 500; word-wrap: break-word;}

    /* --- TIMELINE STYLES --- */
    .timeline-custom { border-left: 2px solid #e2e8f0; margin-left: 10px; padding-left: 25px; padding-top: 5px; }
    .timeline-item { position: relative; margin-bottom: 2rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    
    .timeline-dot { 
        position: absolute; left: -32px; top: 0; 
        width: 16px; height: 16px; border-radius: 50%; 
        border: 2px solid #fff; box-shadow: 0 0 0 1px #cbd5e0; 
    }
    
    .timeline-header { margin-bottom: 0.5rem; }
    .timeline-time { font-size: 0.75rem; color: #a0aec0; display: block; margin-top: 0.25rem; }
    .timeline-user { font-weight: 600; color: #2d3748; font-size: 0.95rem; }
    .timeline-role { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin-left: 6px; }

    .timeline-content { 
        background-color: #fff; 
        border: 1px solid #e2e8f0; 
        padding: 1rem; 
        border-radius: 0.5rem; 
        font-size: 0.95rem; 
        color: #4a5568;
        position: relative;
    }
    /* Panah kecil di kiri box komentar */
    .timeline-content::before {
        content: ''; position: absolute; left: -6px; top: 10px;
        width: 10px; height: 10px; background: #fff;
        border-left: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;
        transform: rotate(45deg);
    }

    /* --- WARNA ROLE (Sesuai Admin) --- */
    /* Super Admin: Merah */
    .role-super_admin .timeline-dot { background-color: #e53e3e; box-shadow: 0 0 0 2px #fed7d7; }
    .role-super_admin .badge-role { background-color: #fed7d7; color: #c53030; }
    
    /* Mahasiswa: Biru */
    .role-mahasiswa .timeline-dot { background-color: #4299e1; box-shadow: 0 0 0 2px #bee3f8; }
    .role-mahasiswa .badge-role { background-color: #ebf8ff; color: #2b6cb0; }

    /* Kepala Unit: Ungu */
    .role-kepala_unit .timeline-dot { background-color: #805ad5; box-shadow: 0 0 0 2px #e9d8fd; }
    .role-kepala_unit .badge-role { background-color: #faf5ff; color: #553c9a; }

    /* Admin Unit / Staff: Hijau */
    .role-admin_unit .timeline-dot { background-color: #38a169; box-shadow: 0 0 0 2px #c6f6d5; }
    .role-admin_unit .badge-role { background-color: #f0fff4; color: #22543d; }
    .role-staff .timeline-dot { background-color: #38a169; box-shadow: 0 0 0 2px #c6f6d5; }
    .role-staff .badge-role { background-color: #f0fff4; color: #22543d; }

    /* Default: Abu */
    .role-unknown .timeline-dot { background-color: #718096; }
    
    /* Status Badges */
    .badge-status { padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #fff; }
    .bg-Pending { background-color: #f6ad55; }
    .bg-Diproses { background-color: #4299e1; }
    .bg-Selesai { background-color: #48bb78; }
    .bg-Ditolak { background-color: #f56565; }
</style>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Tiket /</span> Detail Tiket #{{ $tiket->no_tiket }}
</h4>

{{-- HEADER INFO UTAMA --}}
<div class="card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center p-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">#{{ $tiket->no_tiket }}</h4>
            <div class="text-muted small">
                <i class="ti ti-calendar me-1"></i> Dibuat: {{ $tiket->created_at->translatedFormat('d F Y, H:i') }}
            </div>
        </div>
        <div class="text-end">
            @php
                // Ambil status terakhir dari tabel riwayat
                $statusTerakhir = $tiket->riwayatStatus->sortByDesc('created_at')->first();
                $statusLabel = $statusTerakhir ? $statusTerakhir->status : 'Pending';
            @endphp
            <span class="badge-status bg-{{ $statusLabel }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
@endif

<div class="ticket-layout">
    
    {{-- KOLOM UTAMA (KIRI) --}}
    <div class="ticket-main">

        {{-- 1. FORM BALAS KOMENTAR --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header-custom">
                <i class="ti ti-message-dots me-2"></i> Balas Tiket
            </div>
            <div class="card-body-custom">
                @if(!$tiket->jawaban_id) 
                    <form action="{{ route('mahasiswa.tiket.komentar.store', $tiket->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control" name="komentar" rows="3" placeholder="Tulis balasan atau pertanyaan tambahan..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="input-group w-auto">
                                <input type="file" class="form-control form-control-sm" id="lampiran" name="lampiran">
                            </div>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-send me-1"></i> Kirim
                            </button>
                        </div>
                        <div class="form-text mt-2">Format: JPG, PNG, PDF, DOCX. Maks 5MB.</div>
                    </form>
                @else
                    <div class="alert alert-secondary mb-0 d-flex align-items-center" role="alert">
                        <i class="ti ti-lock me-2 fs-4"></i>
                        <div>Tiket ini telah <strong>ditutup</strong>. Anda tidak dapat mengirim balasan baru.</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. RIWAYAT KOMENTAR (TIMELINE) --}}
        <div class="card shadow-sm">
            <div class="card-header-custom">
                <i class="ti ti-history me-2"></i> Riwayat Percakapan
            </div>
            <div class="card-body-custom">
                <div class="timeline-custom">
                    @forelse($tiket->komentar->sortByDesc('created_at') as $komen)
                        @php
                            // Tentukan Role dan Kelas CSS
                            $role = $komen->pengirim->role ?? 'unknown';
                            $roleClass = 'role-' . $role;
                            $roleLabel = str_replace('_', ' ', $role);
                        @endphp

                        <div class="timeline-item {{ $roleClass }}">
                            <span class="timeline-dot"></span>
                            
                            <div class="timeline-header">
                                <span class="timeline-user">{{ $komen->pengirim->name }}</span>
                                <span class="badge badge-role timeline-role">{{ $roleLabel }}</span>
                                <span class="timeline-time">{{ $komen->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="timeline-content">
                                <p class="mb-0">{!! nl2br(e($komen->komentar)) !!}</p>
                                @if($komen->lampiran)
                                    <div class="mt-3 pt-2 border-top">
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($komen->lampiran) }}" target="_blank" class="text-decoration-none text-primary fs-tiny">
                                            <i class="ti ti-paperclip me-1"></i> {{ basename($komen->lampiran) }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <img src="{{ asset('assets/img/illustrations/girl-sitting-with-laptop.png') }}" alt="No comments" width="120" class="mb-3 opacity-50">
                            <p>Belum ada riwayat percakapan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM SIDEBAR (KANAN) --}}
    <div class="ticket-sidebar">
        
        {{-- INFO TIKET & LAYANAN --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header-custom">Informasi Tiket</div>
            <div class="card-body-custom">
                <dl class="info-grid">
                    <dt>Layanan</dt>
                    <dd class="text-primary">{{ $tiket->layanan->nama }}</dd>

                    <dt>Unit</dt>
                    <dd>{{ $tiket->layanan->unit->nama_unit ?? '-' }}</dd>

                    {{-- PRIORITAS (Dari Tabel Layanan) --}}
                    <dt>Prioritas</dt>
                    <dd>
                        @php
                            $prioVal = $tiket->layanan->prioritas; // 1, 2, 3
                            $prioBadge = 'secondary';
                            $prioText = 'Normal';
                            
                            if($prioVal == 1) { $prioBadge = 'success'; $prioText = 'Rendah'; }
                            if($prioVal == 2) { $prioBadge = 'warning'; $prioText = 'Sedang'; }
                            if($prioVal == 3) { $prioBadge = 'danger'; $prioText = 'Tinggi'; }
                        @endphp
                        <span class="badge bg-label-{{ $prioBadge }}">{{ $prioText }}</span>
                    </dd>
                </dl>

                <hr class="my-3 border-light">
                
                <div class="mb-2">
                    <span class="fw-bold text-muted small text-uppercase">Deskripsi Awal Anda:</span>
                </div>
                <div class="bg-lighter p-3 rounded small text-secondary">
                    {!! nl2br(e($tiket->deskripsi)) !!}
                </div>
                
                @if($tiket->lampiran)
                    <div class="mt-3">
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($tiket->lampiran) }}" target="_blank" class="btn btn-xs btn-outline-primary w-100">
                            <i class="ti ti-file-download me-1"></i> Lihat Lampiran Awal
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- DETAIL UNIK LAYANAN --}}
        @if($detail)
        <div class="card mb-4 shadow-sm">
            <div class="card-header-custom bg-white">
                <span class="text-primary"><i class="ti ti-list-details me-1"></i> Detail Data</span>
            </div>
            <div class="card-body-custom">
                <dl class="info-grid mb-0">
                    @if($tiket->layanan->nama == 'Surat Keterangan Aktif Kuliah')
                        <dt>Keperluan</dt> <dd>{{ $detail->keperluan }}</dd>
                        <dt>Thn. Ajaran</dt> <dd>{{ $detail->tahun_ajaran }}</dd>
                        <dt>Semester</dt> <dd>{{ $detail->semester }}</dd>
                    
                    @elseif($tiket->layanan->nama == 'Reset Akun E-Learning & Siakad' || $tiket->layanan->nama == 'Permintaan Reset Akun E-Mail')
                        <dt>Aplikasi</dt> <dd class="text-uppercase fw-bold">{{ $detail->aplikasi }}</dd>
                        @if(isset($detail->deskripsi))
                           <dt>Keterangan</dt> <dd>{{ $detail->deskripsi }}</dd>
                        @endif

                    @elseif($tiket->layanan->nama == 'Ubah Data Mahasiswa')
                        <dt>Nama Baru</dt> <dd>{{ $detail->data_nama_lengkap }}</dd>
                        <dt>Tempat Lahir</dt> <dd>{{ $detail->data_tmp_lahir }}</dd>
                        <dt>Tanggal Lahir</dt> <dd>{{ \Carbon\Carbon::parse($detail->data_tgl_lhr)->format('d M Y') }}</dd>

                    @elseif($tiket->layanan->nama == 'Request Publikasi Event')
                        <dt>Judul</dt> <dd>{{ $detail->judul }}</dd>
                        <dt>Kategori</dt> <dd>{{ $detail->kategori }}</dd>
                        {{-- PERBAIKAN UTAMA: Menggunakan \Illuminate\Support\Str --}}
                        <dt>Konten</dt> <dd class="fst-italic small">"{{ \Illuminate\Support\Str::limit($detail->konten, 60) }}"</dd>
                        @if($detail->gambar)
                            <dt>Gambar</dt> 
                            <dd><a href="{{ \Illuminate\Support\Facades\Storage::url($detail->gambar) }}" target="_blank" class="text-underline">Lihat Gambar</a></dd>
                        @endif
                    @endif
                </dl>
            </div>
        </div>
        @endif

        {{-- RIWAYAT STATUS --}}
        <div class="card shadow-sm">
            <div class="card-header-custom">
                <i class="ti ti-activity me-1"></i> Timeline Status
            </div>
            <div class="card-body-custom">
                <div class="timeline-custom" style="border-left-color: #cbd5e0;">
                    @foreach($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                        <div class="timeline-item">
                            <span class="timeline-dot" style="background-color: #a0aec0; border-color: #fff;"></span>
                            <div class="timeline-header mb-1">
                                <span class="text-dark fw-bold">{{ $riwayat->status }}</span>
                                <span class="timeline-time d-inline ms-2">{{ $riwayat->created_at->format('d/m/y H:i') }}</span>
                            </div>
                            <div class="small text-muted">
                                Diupdate oleh: {{ $riwayat->user->name ?? 'Sistem' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection