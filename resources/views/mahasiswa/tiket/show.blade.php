@use('Illuminate\Support\Str')
@extends('layouts.layoutMaster')

@section('title', 'Detail Tiket')

@section('content')
<style>
    /* Layout Utama */
    .main-container { 
        background-color: #fff; 
        padding: 32px; 
        border-radius: 12px; 
        box-shadow: 0 0 20px rgba(76, 87, 125, 0.05); 
        width: 100%; 
        margin: 0 auto; 
    }

    /* Header Style */
    .header { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-start; 
        border-bottom: 2px solid #f1f5f9; 
        padding-bottom: 1.5rem; 
        margin-bottom: 2rem; 
    }
    .header h1 { 
        margin: 0 0 5px 0; 
        font-size: 1.75rem; 
        color: #1e293b; 
        font-weight: 700;
    }
    .meta-date {
        color: #64748b;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Tombol */
    .button { 
        display: inline-flex; 
        align-items: center;
        justify-content: center;
        padding: 10px 20px; 
        border-radius: 8px; 
        font-weight: 600; 
        text-decoration: none; 
        cursor: pointer; 
        font-size: 0.9rem; 
        transition: all 0.2s;
        border: none;
    }
    .button-primary { background-color: #3b82f6; color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5); }
    .button-primary:hover { background-color: #2563eb; transform: translateY(-1px); }
    .button-secondary { background-color: #f1f5f9; color: #475569; }
    .button-secondary:hover { background-color: #e2e8f0; }
    .button-sm { padding: 6px 12px; font-size: 0.85rem; gap: 6px; } /* Tambahan untuk tombol kecil */
    
    /* Layout Kolom */
    .ticket-layout { display: flex; flex-wrap: wrap; gap: 2rem; }
    .ticket-main { flex: 2; min-width: 60%; }
    .ticket-sidebar { flex: 1; min-width: 300px; }
    
    /* Card Styling yang Lebih Rapi */
    .card { 
        background-color: #ffffff; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px; 
        margin-bottom: 1.5rem; 
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
    .card-header { 
        padding: 1rem 1.5rem; 
        background-color: #f8fafc; 
        border-bottom: 1px solid #e2e8f0; 
        font-weight: 700; 
        color: #334155;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .card-header i { color: #3b82f6; }
    .card-body { padding: 0; } 
    
    /* KOTAK-KOTAK DATA (Refined Info Grid) */
    .data-row {
        display: flex;
        border-bottom: 1px solid #f1f5f9;
    }
    .data-row:last-child { border-bottom: none; }
    .data-label {
        width: 35%;
        background-color: #fcfcfc;
        padding: 12px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        border-right: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
    }
    .data-value {
        width: 65%;
        padding: 12px 16px;
        font-size: 0.9rem;
        color: #1e293b;
        font-weight: 500;
        line-height: 1.5;
    }
    
    /* Khusus Textarea/Form di dalam Card */
    .card-form-body { padding: 1.5rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #475569; }
    .form-group textarea { 
        width: 100%; 
        padding: 12px; 
        border: 1px solid #cbd5e0; 
        border-radius: 8px; 
        font-size: 0.95rem; 
        min-height: 120px; 
        resize: vertical;
        transition: border-color 0.2s;
    }
    .form-group textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

    /* Image Box Preview KHUSUS (Request Publikasi) */
    .image-preview-container {
        padding: 15px;
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }
    .image-thumbnail-wrapper {
        display: inline-block;
        padding: 5px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .image-thumbnail-wrapper img {
        max-width: 100%;
        height: auto;
        max-height: 250px;
        border-radius: 4px;
        display: block;
    }
    .btn-view-image {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background-color: #fff;
        border: 1px solid #3b82f6;
        color: #3b82f6;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-view-image:hover {
        background-color: #eff6ff;
        color: #2563eb;
    }

    /* Timeline Style */
    .timeline { border-left: 2px solid #e2e8f0; margin: 1.5rem 0 1.5rem 1.5rem; padding-left: 2rem; }
    .timeline-item { position: relative; margin-bottom: 2rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot { 
        position: absolute; 
        left: -39px; 
        top: 4px; 
        width: 14px; 
        height: 14px; 
        border-radius: 50%; 
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e2e8f0;
        background-color: #cbd5e0;
    }
    .timeline-content {
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 8px;
        padding: 1rem;
        position: relative;
    }
    /* Arrow bubble */
    .timeline-content::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 10px;
        width: 10px;
        height: 10px;
        background-color: #f8fafc;
        border-left: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        transform: rotate(45deg);
    }

    .timeline-header { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.85rem; color: #64748b; }
    .timeline-user { font-weight: 700; color: #334155; font-size: 0.95rem; }
    .timeline-body { color: #475569; line-height: 1.5; }

    /* Status Badge Base Style */
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; color: white; text-transform: capitalize; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    
    /* --- WARNA STATUS BARU --- */
    /* Abu-abu */
    .status-diajukan_oleh_pemohon { background-color: #a0aec0; }

    /* Kuning */
    .status-ditangani_oleh_pic,
    .status-diselesaikan_oleh_pic { background-color: #f6ad55; }

    /* Merah */
    .status-dinilai_belum_selesai_oleh_pemohon,
    .status-pemohon_bermasalah { background-color: #f56565; }

    /* Hijau */
    .status-dinilai_selesai_oleh_kepala,
    .status-dinilai_selesai_oleh_pemohon { background-color: #48bb78; }
    /* ------------------------ */

    /* Role Dot Colors */
    .dot-super_admin { background-color: #ef4444; box-shadow: 0 0 0 2px #fca5a5; }
    .dot-mahasiswa { background-color: #3b82f6; box-shadow: 0 0 0 2px #93c5fd; }
    .dot-kepala_unit { background-color: #8b5cf6; box-shadow: 0 0 0 2px #c4b5fd; }
    .dot-admin_unit { background-color: #10b981; box-shadow: 0 0 0 2px #6ee7b7; }

</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="main-container">

        <div class="header">
            <div>
                <h1><span style="color:#94a3b8;">#</span>{{ $tiket->no_tiket }}</h1>
                <div class="meta-date">
                    <i class="ti ti-calendar"></i> Dibuat pada: {{ $tiket->created_at->format('d F Y, H:i') }}
                </div>
            </div>
            <div class="header-info">
                @php
                    $status = $statusSekarang;
                    // Generate class css: status-diajukan_oleh_pemohon, dll.
                    $statusClass = 'status-' . strtolower($status);
                @endphp
                <div style="text-align:right;">
                    <span style="display:block; font-size:0.75rem; color:#64748b; margin-bottom:4px; font-weight:600;">STATUS SAAT INI</span>
                    <span class="status-badge {{ $statusClass }}">
                        {{ str_replace('_', ' ', $status) }}
                    </span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4" style="background-color: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 1rem; border-radius: 8px;">
                <i class="ti ti-check me-1"></i> {{ session('success') }}
            </div>
        @endif

        <div class="ticket-layout">
            <div class="ticket-main">

                <!-- Form Kirim Balasan -->
                @php
                    $isClosed = in_array($statusSekarang, [
                        'Dinilai_Selesai_oleh_Kepala', 
                        'Dinilai_Selesai_oleh_Pemohon'
                    ]);
                @endphp

                @if(!$isClosed)
                <div class="card">
                    <div class="card-header"><i class="ti ti-message-circle-2"></i> Kirim Balasan</div>
                    <div class="card-body card-form-body">
                        <form action="{{ route('mahasiswa.tiket.storeKomentar', $tiket->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="komentar">Pesan Anda</label>
                                <textarea name="komentar" id="komentar" placeholder="Tulis balasan, tambahan informasi, atau pertanyaan..." required></textarea>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <button type="submit" class="button button-primary"><i class="ti ti-send me-2"></i> Kirim Balasan</button>
                                <a href="{{ route('mahasiswa.tiket.index') }}" class="button button-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <div class="alert" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-radius: 8px; padding: 1.5rem; text-align: center;">
                    <i class="ti ti-lock mb-2" style="font-size: 1.5rem;"></i><br>
                    <strong>Tiket ini telah ditutup.</strong><br>
                    Anda tidak dapat mengirim balasan atau mengubah data lagi.
                </div>
                @endif

                <!-- Riwayat Komentar -->
                <div class="card">
                    <div class="card-header"><i class="ti ti-history"></i> Riwayat Diskusi ({{ $tiket->komentar->count() }})</div>
                    <div class="card-body" style="padding: 0;">
                        @if($tiket->komentar->count() > 0)
                            <div class="timeline">
                                @foreach($tiket->komentar as $komen)
                                    @php
                                        $role = $komen->pengirim->role ?? 'unknown';
                                        $dotClass = 'dot-' . $role;
                                    @endphp
                                    <div class="timeline-item">
                                        <span class="timeline-dot {{ $dotClass }}"></span>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <span class="timeline-user">
                                                    {{ $komen->pengirim->name }} 
                                                    <span style="font-weight:400; color: #94a3b8;">
                                                        ({{ str_replace('_', ' ', ucwords($role)) }})
                                                    </span>
                                                </span>
                                                <span>{{ $komen->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="timeline-body">
                                                {!! nl2br(e($komen->komentar)) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="padding: 2rem; text-align: center; color: #94a3b8;">
                                <em>Belum ada diskusi pada tiket ini.</em>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="ticket-sidebar">
                <!-- Info Pemohon -->
                <div class="card">
                    <div class="card-header"><i class="ti ti-user"></i> Informasi Pemohon</div>
                    <div class="card-body">
                        <div class="data-row">
                            <div class="data-label">Nama</div>
                            <div class="data-value">{{ $tiket->pemohon->name ?? 'N/A' }}</div>
                        </div>
                        <div class="data-row">
                            <div class="data-label">NIM</div>
                            <div class="data-value">{{ $tiket->pemohon->mahasiswa->nim ?? 'N/A' }}</div>
                        </div>
                        <div class="data-row">
                            <div class="data-label">Prodi</div>
                            <div class="data-value">{{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Detail Layanan / Informasi Tiket -->
                <div class="card">
                    <div class="card-header"><i class="ti ti-file-description"></i> Detail Permohonan</div>
                    <div class="card-body">
                        <div class="data-row">
                            <div class="data-label">Layanan</div>
                            <div class="data-value">{{ $tiket->layanan->nama ?? 'N/A' }}</div>
                        </div>
                        <div class="data-row">
                            <div class="data-label">Unit</div>
                            <div class="data-value">{{ $tiket->layanan->unit->nama_unit ?? 'N/A' }}</div>
                        </div>
                        <div class="data-row">
                            <div class="data-label">Deskripsi</div>
                            <div class="data-value">{!! nl2br(e($tiket->deskripsi)) !!}</div>
                        </div>

                        <!-- Lampiran Umum -->
                        @if($tiket->lampiran)
                        <div class="data-row">
                            <div class="data-label">Lampiran</div>
                            <div class="data-value">
                                <a href="{{ asset('storage/' . $tiket->lampiran) }}" target="_blank" class="text-primary" style="text-decoration: underline; display: flex; align-items: center; gap: 5px;">
                                    <i class="ti ti-paperclip"></i> Unduh File
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- DETAIL KHUSUS BERDASARKAN TIPE LAYANAN -->
                        @if ($detail)
                            
                            <!-- Surat Keterangan Aktif -->
                            @if (Str::contains($tiket->layanan->nama, 'Surat Keterangan Aktif Kuliah'))
                                <div class="data-row"><div class="data-label">Keperluan</div><div class="data-value">{{ $detail->keperluan }}</div></div>
                                <div class="data-row"><div class="data-label">Tahun Ajar</div><div class="data-value">{{ $detail->tahun_ajaran }}</div></div>
                                <div class="data-row"><div class="data-label">Semester</div><div class="data-value">{{ $detail->semester }}</div></div>
                                @if($detail->keperluan_lainnya)
                                <div class="data-row"><div class="data-label">Ket. Lain</div><div class="data-value">{{ $detail->keperluan_lainnya }}</div></div>
                                @endif

                            <!-- Reset Akun -->
                            @elseif(Str::contains($tiket->layanan->nama, 'Reset Akun'))
                                <div class="data-row"><div class="data-label">Aplikasi</div><div class="data-value">{{ $detail->aplikasi }}</div></div>
                                <div class="data-row"><div class="data-label">Masalah</div><div class="data-value">{{ $detail->deskripsi }}</div></div>

                            <!-- Ubah Data -->
                            @elseif(Str::contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                                <div class="data-row"><div class="data-label">Nama Baru</div><div class="data-value">{{ $detail->data_nama_lengkap ?? '-' }}</div></div>
                                <div class="data-row"><div class="data-label">Tmp Lahir</div><div class="data-value">{{ $detail->data_tmp_lahir ?? '-' }}</div></div>
                                <div class="data-row"><div class="data-label">Tgl Lahir</div><div class="data-value">{{ $detail->data_tgl_lhr ?? '-' }}</div></div>

                            <!-- Request Publikasi -->
                            @elseif(Str::contains($tiket->layanan->nama, 'Publikasi'))
                                <div class="data-row"><div class="data-label">Judul</div><div class="data-value">{{ $detail->judul }}</div></div>
                                <div class="data-row"><div class="data-label">Kategori</div><div class="data-value">{{ $detail->kategori }}</div></div>
                            @endif

                        @endif
                    </div>

                    <!-- TAMPILAN KHUSUS GAMBAR PUBLIKASI (Kotak Preview & Button) -->
                    @if ($detail && Str::contains($tiket->layanan->nama, 'Publikasi') && $detail->gambar)
                    <div class="image-preview-container">
                        <h6 style="color:#475569; margin-bottom:8px; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                            Lampiran Gambar
                        </h6>
                        
                        <!-- Thumbnail Box -->
                        <div class="image-thumbnail-wrapper">
                            <a href="{{ asset('storage/' . $detail->gambar) }}" target="_blank" title="Klik untuk memperbesar">
                                <img src="{{ asset('storage/' . $detail->gambar) }}" alt="Gambar Publikasi">
                            </a>
                        </div>

                        <!-- Tombol Lihat Gambar -->
                        <div>
                            <a href="{{ asset('storage/' . $detail->gambar) }}" target="_blank" class="btn-view-image">
                                <i class="ti ti-photo"></i> Lihat Gambar
                            </a>
                        </div>
                    </div>
                    @endif
                    <!-- Akhir Tampilan Gambar -->

                </div>

                <div class="card">
                    <div class="card-header"><i class="ti ti-activity"></i> Riwayat Status</div>
                    <div class="card-body">
                        <div class="timeline" style="margin: 1.5rem;">
                            @foreach ($tiket->riwayatStatus as $riwayat)
                                <div style="position: relative; padding-left: 1.5rem; margin-bottom: 1.5rem; border-left: 2px dashed #e2e8f0;">
                                    <div style="position: absolute; left: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: #94a3b8;"></div>
                                    <div style="font-weight: 600; color: #334155;">{{ str_replace('_', ' ', $riwayat->status) }}</div>
                                    <div style="font-size: 0.8rem; color: #64748b;">
                                        {{ $riwayat->created_at->format('d M Y, H:i') }} 
                                        <span style="color: #cbd5e0;">•</span> 
                                        Oleh: {{ $riwayat->user->name ?? 'Sistem' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection