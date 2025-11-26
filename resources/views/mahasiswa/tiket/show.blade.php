@use('Illuminate\Support\Str')
@extends('layouts.layoutMaster')

@section('title', 'Detail Tiket')

@section('content')
<style>
    /* Layout Utama */
    .main-container { background-color: white; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; margin: 0 auto; }
    .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .header h1 { margin: 0; font-size: 1.5rem; }
    .header-info { text-align: right; }
    
    .button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; border: 1px solid transparent; border-radius: 5px; font-weight: 600; text-decoration: none; cursor: pointer; font-size: 0.95rem; }
    .button-primary { background-color: #4299e1; color: white; border-color: #4299e1; }
    .button-secondary { background-color: #e2e8f0; color: #2d3748; border-color: #cbd5e0; }
    .button-sm { padding: 6px 12px; font-size: 0.85rem; }
    .button-success { background-color: #48bb78; color: white; border-color: #48bb78; }
    .button-danger { background-color: #f56565; color: white; border-color: #f56565; }
    
    .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
    .alert-success { color: #2f855a; background-color: #c6f6d5; }
    .alert-error { color: #9b2c2c; background-color: #fed7d7; }
    .alert-error ul { margin: 0; padding-left: 20px; }
    
    .ticket-layout { display: flex; flex-wrap: wrap; gap: 2rem; }
    .ticket-main { flex: 2; min-width: 400px; }
    .ticket-sidebar { flex: 1; min-width: 300px; }
    
    .card { background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 1.5rem; }
    .card-header { padding: 1rem 1.5rem; background-color: #f7fafc; border-bottom: 1px solid #e2e8f0; font-weight: 600; }
    .card-body { padding: 1.5rem; }
    
    .info-grid { display: grid; grid-template-columns: 150px 1fr; gap: 1rem; }
    .info-grid dt { font-weight: 600; color: #4a5568; }
    .info-grid dd { margin: 0; }
    
    .data-row { display: flex; border-bottom: 1px solid #f1f5f9; }
    .data-row:last-child { border-bottom: none; }
    .data-label { width: 35%; background-color: #fcfcfc; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; color: #64748b; border-right: 1px solid #f1f5f9; display: flex; align-items: center; }
    .data-value { width: 65%; padding: 12px 16px; font-size: 0.9rem; color: #1e293b; font-weight: 500; line-height: 1.5; }

    .image-preview-box { padding: 1.5rem; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center; margin-top: -1px; }
    .img-thumbnail-container { display: inline-block; border: 1px solid #e2e8f0; padding: 6px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 100%; transition: transform 0.2s; margin-bottom: 12px; }
    .img-thumbnail-container:hover { transform: scale(1.01); }
    .img-thumbnail-container img { max-width: 100%; height: auto; border-radius: 4px; display: block; max-height: 200px; object-fit: contain; }
    
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; }
    .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.95rem; box-sizing: border-box; min-height: 120px; resize: vertical; }
    
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
    <div class="main-container">

        <div class="header">
            <div>
                <h1>Tiket #{{ $tiket->no_tiket }}</h1>
                <p style="margin:0; color: #718096;">Dibuat pada: {{ $tiket->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="header-info">
                @php
                    $status = $statusSekarang;
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

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Oops! Ada beberapa masalah:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="ticket-layout">
            <div class="ticket-main">
                
                @if($statusSekarang == 'Diselesaikan_oleh_PIC')
                
                {{-- ================================================= --}}
                {{--             TAMBAHAN FITUR TIMER START            --}}
                {{-- ================================================= --}}
                @php
                    // Ambil data timer langsung dari Cache
                    $deadlineMhs = \Illuminate\Support\Facades\Cache::get('tiket_timer_' . $tiket->id);
                @endphp

                @if($deadlineMhs)
                <div class="card mb-4" style="background-color: #fffbeb; border: 1px solid #fcd34d;">
                    <div class="card-body text-center py-3">
                        <h5 style="color: #b45309; font-weight: bold; margin-bottom: 8px;">⏳ Konfirmasi Otomatis</h5>
                        <p style="color: #b45309; font-size: 0.9rem; margin-bottom: 5px;">Tiket ini akan otomatis ditutup jika tidak ada respon dalam:</p>
                        
                        <div id="student-countdown" style="font-size: 1.5rem; font-weight: 800; color: #d97706; margin: 10px 0;">
                            Memuat Waktu...
                        </div>
                        
                        <small style="color: #92400e;">Batas Waktu: {{ \Carbon\Carbon::parse($deadlineMhs)->format('d M Y, H:i:s') }}</small>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var deadline = new Date("{{ \Carbon\Carbon::parse($deadlineMhs)->format('Y-m-d H:i:s') }}").getTime();
                        var x = setInterval(function() {
                            var now = new Date().getTime();
                            var distance = deadline - now;
                            
                            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            
                            document.getElementById("student-countdown").innerHTML = 
                                days + " Hari " + hours + " Jam " + minutes + " Menit " + seconds + " Detik";
                                
                            if (distance < 0) {
                                clearInterval(x);
                                document.getElementById("student-countdown").innerHTML = "WAKTU HABIS - Sedang Memproses...";
                                location.reload();
                            }
                        }, 1000);
                    });
                </script>
                @endif
                {{-- ================================================= --}}
                {{--             TAMBAHAN FITUR TIMER END              --}}
                {{-- ================================================= --}}

                <div class="card" style="border: 1px solid #4299e1;">
                    <div class="card-header" style="background-color: #ebf8ff; color: #2c5282;">Konfirmasi Penyelesaian</div>
                    <div class="card-body pt-4">
                        <p>PIC telah menandai tiket ini sebagai selesai. Apakah Anda menyetujui hasil pengerjaan ini?</p>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            
                            {{-- Tombol Selesai --}}
                            <form action="{{ route('mahasiswa.tiket.updateStatus', $tiket->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="Dinilai_Selesai_oleh_Pemohon">
                                <button type="submit" class="button button-success" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan tiket ini?')">
                                    <i class="ti ti-check"></i> Dinilai Selesai oleh Pemohon
                                </button>
                            </form>

                            {{-- Tombol Belum Selesai --}}
                            <form action="{{ route('mahasiswa.tiket.updateStatus', $tiket->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="Dinilai_Belum_Selesai_oleh_Pemohon">
                                <button type="submit" class="button button-danger" onclick="return confirm('Status tiket akan berubah menjadi Dinilai Belum Selesai oleh Pemohon. Lanjutkan?')">
                                    <i class="ti ti-x"></i> Dinilai Belum Selesai oleh Pemohon
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
                @endif

                @php
                    $isClosed = in_array($statusSekarang, [
                        'Dinilai_Selesai_oleh_Kepala', 
                        'Dinilai_Selesai_oleh_Pemohon'
                    ]);
                @endphp

                @if(!$isClosed)
                <div class="card">
                    <div class="card-header">Kirim Balasan</div>
                    <div class="card-body">
                        <form action="{{ route('mahasiswa.tiket.storeKomentar', $tiket->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="form-group">
                                <label for="komentar">Tulis Pesan Anda</label>
                                <textarea name="komentar" id="komentar" placeholder="Tulis balasan atau tambahan informasi..." required></textarea>
                            </div>

                            <button type="submit" class="button button-primary">Kirim Balasan</button>
                            <a href="{{ route('mahasiswa.tiket.index') }}" class="button button-secondary" style="float: right;">Kembali ke Daftar</a>
                        </form>
                    </div>
                </div>
                @else
                <div class="alert alert-success">
                    <strong>Tiket ini telah ditutup.</strong> Anda tidak dapat mengirim balasan lagi.
                </div>
                @endif

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

            </div>

            <div class="ticket-sidebar">
                <div class="card">
                    <div class="card-header">Informasi Pemohon</div>
                    <div class="card-body">
                        <dl class="info-grid">
                            <dt>Nama</dt> <dd>{{ $tiket->pemohon->name ?? 'N/A' }}</dd>
                            <dt>Email</dt> <dd>{{ $tiket->pemohon->email ?? 'N/A' }}</dd>
                            <dt>NIM</dt> <dd>{{ $tiket->pemohon->mahasiswa->nim ?? 'N/A' }}</dd>
                            <dt>Jurusan</dt> <dd>{{ $tiket->pemohon->mahasiswa->programStudi->jurusan->nama_jurusan ?? 'N/A' }}</dd>
                            <dt>Prodi</dt> <dd>{{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Informasi Tiket</div>
                    <div class="card-body">
                        <dl class="info-grid">
                            <dt>Layanan</dt> <dd>{{ $tiket->layanan->nama ?? 'N/A' }}</dd>
                            <dt>Unit</dt> <dd>{{ $tiket->layanan->unit->nama_unit ?? 'N/A' }}</dd>
                            <dt>Deskripsi</dt> <dd>{!! nl2br(e($tiket->deskripsi)) !!}</dd>
                            @if($tiket->lampiran)
                                <dt>Lampiran</dt>
                                <dd><a href="{{ asset('storage/' . $tiket->lampiran) }}" target="_blank" class="text-primary">Lihat File</a></dd>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Bagian Detail Layanan di View --}}
                @if ($detailLayanan)
                    <div class="card">
                        <div class="card-header">Detail Layanan: {{ $tiket->layanan->nama }}</div>
                        <div class="card-body">
                            <dl class="info-grid">
                                {{-- 1. Surat Keterangan Aktif Kuliah --}}
                                @if (Str::contains($tiket->layanan->nama, 'Surat Keterangan Aktif Kuliah'))
                                    <dt>Keperluan</dt>
                                    <dd>{{ $detailLayanan->keperluan }}</dd>

                                    <dt>Tahun Ajaran</dt>
                                    <dd>{{ $detailLayanan->tahun_ajaran }}</dd>

                                    <dt>Semester</dt>
                                    <dd>{{ $detailLayanan->semester }}</dd>

                                    @if ($detailLayanan->keperluan_lainnya)
                                        <dt>Keperluan Lainnya</dt>
                                        <dd>{{ $detailLayanan->keperluan_lainnya }}</dd>
                                    @endif

                                    {{-- 2. Reset Akun --}}
                                @elseif(Str::contains($tiket->layanan->nama, 'Reset Akun'))
                                    <dt>Aplikasi</dt>
                                    <dd>{{ $detailLayanan->aplikasi }}</dd>

                                    <dt>Deskripsi Masalah</dt>
                                    <dd>{{ $detailLayanan->deskripsi }}</dd>

                                    {{-- 3. Ubah Data Mahasiswa --}}
                                @elseif(Str::contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                                    <dt>Data Nama Lengkap</dt>
                                    <dd>{{ $detailLayanan->data_nama_lengkap ?? '-' }}</dd>

                                    <dt>Tempat Lahir Baru</dt>
                                    <dd>{{ $detailLayanan->data_tmp_lahir ?? '-' }}</dd>

                                    <dt>Tanggal Lahir Baru</dt>
                                    <dd>{{ $detailLayanan->data_tgl_lhr ?? '-' }}</dd>

                                    {{-- 4. Request Publikasi --}}
                                @elseif(Str::contains($tiket->layanan->nama, 'Request Publikasi') || Str::contains($tiket->layanan->nama, 'Publikasi'))
                                    <dt>Judul / Topik</dt>
                                    <dd>{{ $detailLayanan->judul }}</dd>

                                    <dt>Kategori</dt>
                                    <dd>{{ $detailLayanan->kategori }}</dd>

                                    <dt>Konten / Isi</dt>
                                    <dd>{!! nl2br(e($detailLayanan->konten)) !!}</dd>
                                    
                                @if ($detail->gambar)
                                    <dt>Lampiran</dt>
                                    @php
                                        $path = storage_path('app/public/' . $detail->gambar);
                                        $imageData = null;
                                        $mimeType = 'image/jpeg'; 

                                        if (file_exists($path)) {
                                            $imageData = base64_encode(file_get_contents($path));
                                            $extension = pathinfo($path, PATHINFO_EXTENSION);
                                            if($extension == 'png') $mimeType = 'image/png';
                                            elseif($extension == 'jpg' || $extension == 'jpeg') $mimeType = 'image/jpeg';
                                        }
                                    @endphp

                                    @if($imageData)
                                    <dd>
                                        {{-- PREVIEW GAMBAR (MAHASISWA) --}}
                                        <div style="margin-bottom: 10px; border: 1px solid #e2e8f0; padding: 5px; border-radius: 5px; background: #f7fafc; text-align: center;">
                                            <img src="data:{{ $mimeType }};base64,{{ $imageData }}" alt="Preview Lampiran" style="max-width: 100%; max-height: 150px; border-radius: 4px;">
                                        </div>
                                        {{-- END PREVIEW --}}

                                        <a href="data:{{ $mimeType }};base64,{{ $imageData }}" target="_blank" class="btn-view-image">
                                            <i class="ti ti-photo"></i> Lihat Gambar
                                        </a>
                                    </dd>
                                    @endif
                                @endif
                            @endif
                        </dl>
                    </div>
                </div>
                @endif

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
            </div>
        </div>
    </div>
</div>
@endsection