@extends('layouts/contentNavbarLayout')
@use('Illuminate\Support\Str')
@section('title', 'Detail Tiket ' . $tiket->no_tiket)

@section('content')
<style>
    /* --- CSS Status Badge & Prioritas (Disempurnakan) --- */
    .status-badge, .prioritas-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
        line-height: 1.2;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .status-diajukan_oleh_pemohon { background-color: #e2e8f0; color: #4a5568; } /* Light Gray */
    .status-ditangani_oleh_pic { background-color: #f6ad55; color: white; } /* Orange */
    .status-diselesaikan_oleh_pic { background-color: #4299e1; color: white; } /* Blue */
    .status-dinilai_belum_selesai_oleh_pemohon, .status-pemohon_bermasalah, .status-ditolak { background-color: #f56565; color: white; } /* Red */
    .status-dinilai_selesai_oleh_kepala, .status-dinilai_selesai_oleh_pemohon { background-color: #48bb78; color: white; } /* Green */
    .prioritas-rendah { background-color: #a0aec0; color: white; }
    .prioritas-sedang { background-color: #48bb78; color: white; }
    .prioritas-tinggi { background-color: #f56565; color: white; }
    
    /* --- Timeline Style (Disesuaikan) --- */
    .timeline { position: relative; padding-left: 20px; }
    .timeline-item { position: relative; padding-left: 20px; padding-bottom: 25px; }
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background-color: #e2e8f0; /* Garis vertikal abu-abu muda */
        margin-left: -2px; /* Pusatkan garis */
    }
    .timeline-dot {
        position: absolute;
        left: -8px;
        top: 3px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #4299e1; /* Default blue dot */
        border: 3px solid #fff; /* Border putih untuk efek timpa */
        z-index: 10;
    }
    .timeline-time { font-size: 0.85rem; color: #718096; margin-bottom: 0.25rem; }
    .timeline-title { font-weight: 600; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    
    /* Dot dan Body Komentar berdasarkan Role */
    .dot-super_admin { background-color: #e53e3e !important; }
    .body-super_admin { background-color: #fef2f2 !important; border-color: #fbd7d7 !important; }
    .dot-mahasiswa { background-color: #4299e1 !important; }
    .body-mahasiswa { background-color: #ebf8ff !important; border-color: #bee3f8 !important; }
    .dot-kepala_unit { background-color: #805ad5 !important; }
    .body-kepala_unit { background-color: #faf5ff !important; border-color: #e9d8fd !important; }
    .dot-admin_unit { background-color: #38a169 !important; }
    .body-admin_unit { background-color: #f0fff4 !important; border-color: #c6f6d5 !important; }
    .timeline-body-comment { border: 1px solid; padding: 1rem; border-radius: 6px; }
    
    /* Grid untuk Detail Layanan */
    .info-grid dt { font-weight: 600; color: #4a5568; margin-top: 5px; }
    .info-grid dd { margin-bottom: 5px; }
    .info-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 5px 15px; }
    .info-grid dd, .info-grid dt { margin: 0; padding: 0; }
    
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Monitoring /</span> Detail Tiket
    </h4>

    <div class="row">
        {{-- KOLOM KIRI (Tindak Lanjut, Komentar, Riwayat) --}}
        <div class="col-lg-8 col-md-12">
            
            {{-- 1. Tindak Lanjut Kepala Unit --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Aksi & Validasi Kepala Unit</h5>
                    <span class="badge bg-label-primary">{{ $tiket->layanan->unit->nama_unit ?? 'Unit Tidak Ditemukan' }}</span>
                </div>
                <div class="card-body">
                    @php
                        $currentStatus = $tiket->statusTerbaru?->status ?? 'Diajukan_oleh_Pemohon';
                        // Hanya bisa edit jika status adalah "Pemohon Bermasalah"
                        $canEdit = ($currentStatus === 'Pemohon_Bermasalah'); 
                    @endphp

                    @if(!$canEdit)
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="bx bx-lock-alt me-2"></i>
                            <div>
                                Menu Terkunci: Aksi hanya dapat dilakukan saat status tiket adalah "Pemohon Bermasalah" dan membutuhkan validasi Anda.
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('kepala-unit.monitoring.update', $tiket->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Pilihan Validasi Status</label>
                                <select name="status" class="form-select" {{ $canEdit ? '' : 'disabled' }}>
                                    <option value="" selected disabled>-- Pilih Keputusan --</option>
                                    <option value="Dinilai_Selesai_oleh_Kepala">Setujui Selesai (Final)</option>
                                </select>
                                @if($canEdit)
                                    <div class="form-text text-primary">Silakan validasi tiket ini untuk penyelesaian akhir.</div>
                                @else
                                     <div class="form-text text-danger">Status saat ini: "{{ str_replace('_', ' ', $currentStatus) }}". Tidak ada tindakan yang diperlukan dari Kepala Unit.</div>
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

            {{-- 2. Diskusi & Riwayat Komentar --}}
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Diskusi & Komentar</h5>
                </div>
                <div class="card-body">
                    {{-- Form Komentar --}}
                    <form action="{{ route('kepala-unit.monitoring.komentar', $tiket->id) }}" method="POST" class="mb-5">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Balas / Tambah Catatan Anda</label>
                            <textarea name="komentar" class="form-control" rows="3" placeholder="Tulis pesan untuk pemohon atau catatan internal..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bx bx-send me-1"></i> Kirim Komentar
                            </button>
                        </div>
                    </form>

                    {{-- Riwayat Komentar --}}
                    <h6 class="mb-3 text-muted border-bottom pb-2">Riwayat Komentar ({{ $tiket->komentar->count() }})</h6>
                    <div class="timeline">
                        @forelse($tiket->komentar as $komen)
                            @php
                                $role = $komen->pengirim->role ?? 'unknown';
                                $dotClass = 'dot-' . $role;
                                $bodyClass = 'body-' . $role;
                            @endphp
                            <div class="timeline-item">
                                <span class="timeline-dot {{ $dotClass }}"></span>
                                <div class="timeline-content">
                                    <div class="timeline-time">{{ $komen->created_at->format('d M Y, H:i') }}</div>
                                    <div class="timeline-title">
                                        {{ $komen->pengirim->name ?? 'Sistem' }} 
                                        (<strong class="text-primary" style="text-transform: capitalize;">{{ str_replace('_', ' ', $role) }}</strong>)
                                    </div>
                                    <div class="timeline-body timeline-body-comment {{ $bodyClass }}">
                                        <p class="m-0">{!! nl2br(e($komen->komentar)) !!}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">Belum ada komentar dalam diskusi ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 3. Riwayat Status --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Riwayat Status</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach ($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                            @php
                                $dotColor = '#718096'; // Default Gray
                                $statusLower = strtolower($riwayat->status);

                                if (str_contains($statusLower, 'diajukan')) {
                                    $dotColor = '#a0aec0'; // Light Gray
                                } elseif (str_contains($statusLower, 'ditangani') || str_contains($statusLower, 'diselesaikan')) {
                                    $dotColor = '#f6ad55'; // Orange
                                } elseif (str_contains($statusLower, 'belum_selesai') || str_contains($statusLower, 'bermasalah') || str_contains($statusLower, 'ditolak')) {
                                    $dotColor = '#f56565'; // Red
                                } elseif (str_contains($statusLower, 'selesai')) {
                                    $dotColor = '#48bb78'; // Green
                                }
                            @endphp
                            <div class="timeline-item">
                                <span class="timeline-dot" style="background-color: {{ $dotColor }};"></span>
                                <div class="timeline-content">
                                    <div class="timeline-time">{{ $riwayat->created_at->format('d M Y, H:i') }}</div>
                                    <div class="timeline-title">
                                        Status: 
                                        <span class="status-badge" style="background-color: {{ $dotColor }}; color: white; text-transform: capitalize;">
                                            {{ str_replace('_', ' ', $riwayat->status) }}
                                        </span>
                                    </div>
                                    <div class="timeline-body">
                                        <p class="m-0">Oleh: **{{ $riwayat->user->name ?? 'Sistem' }}**</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (Sidebar: Info Tiket, Data Pemohon, Detail Layanan, Deskripsi, Timer) --}}
        <div class="col-lg-4 col-md-12">
            
            {{-- 1. INFORMASI TIKET (POSISI PALING ATAS) --}}
            <div class="card mb-4">
                <div class="card-header bg-label-info text-dark fw-bold">
                    <i class="bx bx-ticket me-1"></i> Detail Utama Tiket
                </div>
                <div class="card-body pt-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">No Tiket</dt>
                        <dd class="col-sm-8 text-end fw-bold text-primary">{{ $tiket->no_tiket }}</dd>

                        <dt class="col-sm-4">Layanan</dt>
                        <dd class="col-sm-8 text-end">{{ $tiket->layanan->nama }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8 text-end">
                            @php
                                $st = $tiket->statusTerbaru?->status ?? 'Diajukan_oleh_Pemohon';
                                $cls = 'status-' . strtolower($st);
                            @endphp
                            <span class="status-badge {{ $cls }}">{{ str_replace('_', ' ', $st) }}</span>
                        </dd>

                        <dt class="col-sm-4 mt-2">Prioritas</dt>
                        <dd class="col-sm-8 mt-2 text-end">
                            @php
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
                            <span class="prioritas-badge {{ $prioClassShow }}">{{ $prioLabelShow }}</span>
                        </dd>
                    </dl>
                </div>
            </div>

            {{-- 2. DATA PEMOHON (POSISI DIPINDAH KE SINI) --}}
            <div class="card mb-4">
                <div class="card-header bg-label-secondary text-dark fw-bold">
                    <i class="bx bx-user-circle me-1"></i> Data Pemohon
                </div>
                <div class="card-body text-center">
                    <div class="avatar avatar-xl mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-info">
                            {{ strtoupper(substr($tiket->pemohon->name ?? 'U', 0, 2)) }}
                        </span>
                    </div>
                    <h5 class="mb-1">{{ $tiket->pemohon->name }}</h5>
                    <p class="text-muted mb-0">{{ $tiket->pemohon->email }}</p>
                    @if($tiket->pemohon->mahasiswa)
                        <hr class="my-3">
                        <div class="text-start small">
                            <dl class="row mb-0">
                                <dt class="col-4 text-muted">NIM:</dt>
                                <dd class="col-8 text-end fw-bold">{{ $tiket->pemohon->mahasiswa->nim }}</dd>
                                <dt class="col-4 text-muted">Prodi:</dt>
                                <dd class="col-8 text-end">{{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? '-' }}</dd>
                            </dl>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. Detail Layanan --}}
            @if(isset($detailLayanan))
                <div class="card mb-4">
                    <div class="card-header bg-label-warning text-dark fw-bold">
                        <i class="bx bx-list-ul me-1"></i> Detail Permintaan Layanan
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">{{ $tiket->layanan->nama }}</h6>
                        <div class="info-grid">
                            {{-- 1. Surat Keterangan Aktif Kuliah --}}
                            @if (Str::contains($tiket->layanan->nama, 'Surat Keterangan Aktif Kuliah'))
                                <dt>Keperluan</dt><dd>{{ $detailLayanan->keperluan }}</dd>
                                <dt>Tahun Ajaran</dt><dd>{{ $detailLayanan->tahun_ajaran }}</dd>
                                <dt>Semester</dt><dd>{{ $detailLayanan->semester }}</dd>
                                @if ($detailLayanan->keperluan_lainnya)
                                    <dt>Keperluan Lainnya</dt><dd>{{ $detailLayanan->keperluan_lainnya }}</dd>
                                @endif

                            {{-- 2. Reset Akun --}}
                            @elseif(Str::contains($tiket->layanan->nama, 'Reset Akun'))
                                <dt>Aplikasi</dt><dd>{{ $detailLayanan->aplikasi }}</dd>
                                <dt>Deskripsi Masalah</dt><dd>{{ $detailLayanan->deskripsi }}</dd>

                            {{-- 3. Ubah Data Mahasiswa --}}
                            @elseif(Str::contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                                <dt>Data Nama Lengkap</dt><dd>{{ $detailLayanan->data_nama_lengkap ?? '-' }}</dd>
                                <dt>Tempat Lahir Baru</dt><dd>{{ $detailLayanan->data_tmp_lahir ?? '-' }}</dd>
                                <dt>Tanggal Lahir Baru</dt><dd>{{ $detailLayanan->data_tgl_lhr ?? '-' }}</dd>

                            {{-- 4. Request Publikasi --}}
                            @elseif(Str::contains($tiket->layanan->nama, 'Request Publikasi') || Str::contains($tiket->layanan->nama, 'Publikasi'))
                                <dt>Judul / Topik</dt><dd>{{ $detailLayanan->judul }}</dd>
                                <dt>Kategori</dt><dd>{{ $detailLayanan->kategori }}</dd>
                                <dt class="col-span-2">Konten / Isi</dt>
                                <dd class="col-span-2">{!! nl2br(e($detailLayanan->konten)) !!}</dd>
                            @endif
                        </div>

                        {{-- Lampiran Gambar (Jika ada) --}}
                        @if(isset($detailLayanan->gambar) && $detailLayanan->gambar)
                            <hr class="my-3">
                            <span class="d-block fw-bold mb-1">Lampiran Gambar:</span>
                            <div class="mb-2 text-center border rounded p-1 bg-light">
                                <img src="{{ asset('storage/' . $detailLayanan->gambar) }}" alt="Preview" class="img-fluid rounded" style="max-height: 150px; object-fit: cover; width: 100%;">
                            </div>
                            <a href="{{ asset('storage/' . $detailLayanan->gambar) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                <i class='bx bx-image me-1'></i> Lihat Gambar Penuh
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 4. Deskripsi Awal --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Deskripsi Awal Permintaan</h6>
                </div>
                <div class="card-body">
                    <p class="card-text text-break">{{ $tiket->deskripsi }}</p>
                </div>
            </div>

            {{-- 5. FITUR TIMER (Jika Aktif) --}}
            @php
                $cacheKey = 'tiket_timer_' . $tiket->id;
                $deadline = \Illuminate\Support\Facades\Cache::get($cacheKey);
                $isTimerActive = ($tiket->statusTerbaru?->status === 'Diselesaikan_oleh_PIC' && $deadline);
            @endphp

            @if($isTimerActive)
                <div class="card mb-4 border border-warning shadow-sm">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 text-white fw-bold"><i class='bx bx-timer'></i> Timer Validasi Pemohon</h6>
                    </div>
                    <div class="card-body pt-3 text-center">
                        <p class="mb-1 text-muted small">Otomatis selesai pada:</p>
                        <h6 class="fw-bold text-dark mb-2">
                            {{ \Carbon\Carbon::parse($deadline)->format('d M Y, H:i:s') }}
                        </h6>
                        
                        <div id="admin-countdown" class="alert alert-warning p-2 mb-3 fw-bold" style="font-size: 1.2rem;">
                            Memuat...
                        </div>

                        <hr class="my-2">
                        
                        <button class="btn btn-sm btn-outline-primary w-100 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#settingTimer">
                            <i class='bx bx-cog'></i> Atur Ulang Timer
                        </button>

                        <div class="collapse" id="settingTimer">
                            <form action="{{ route('kepala-unit.monitoring.update-timer', $tiket->id) }}" method="POST" class="bg-light p-2 rounded border">
                                @csrf
                                @method('PUT')
                                <label class="form-label small text-start w-100 fw-bold">Set Durasi Baru</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="amount" class="form-control" placeholder="Angka" min="1" required>
                                    
                                    <select name="unit" class="form-select" style="max-width: 100px;">
                                        <option value="days">Hari</option>
                                        <option value="hours">Jam</option>
                                        <option value="minutes">Menit</option>
                                        <option value="seconds">Detik</option>
                                    </select>
                                    
                                    <button class="btn btn-primary" type="submit">Set</button>
                                </div>
                                <div class="form-text text-start text-muted" style="font-size: 0.7rem;">
                                    Timer di-reset mulai dari SEKARANG.
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var deadlineStr = "{{ \Carbon\Carbon::parse($deadline)->format('Y-m-d H:i:s') }}";
                        // Pastikan format date string kompatibel dengan JS Date
                        var countDownDate = new Date(deadlineStr.replace(' ', 'T')).getTime(); 

                        var x = setInterval(function() {
                            var now = new Date().getTime();
                            var distance = countDownDate - now;

                            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            // Tambahkan leading zero jika perlu
                            var pad = (num) => num.toString().padStart(2, '0');

                            document.getElementById("admin-countdown").innerHTML = 
                                (days > 0 ? days + "h " : "") + pad(hours) + "j " + pad(minutes) + "m " + pad(seconds) + "d";

                            if (distance < 0) {
                                clearInterval(x);
                                document.getElementById("admin-countdown").innerHTML = "Waktu Habis. Memproses status...";
                                document.getElementById("admin-countdown").classList.remove('alert-warning');
                                document.getElementById("admin-countdown").classList.add('alert-success');
                                
                                // Refresh halaman 3 detik setelah waktu habis untuk memicu update status
                                setTimeout(function() {
                                    window.location.reload();
                                }, 3000);
                            }
                        }, 1000);
                    });
                </script>
            @endif
            
            <a href="{{ route('kepala-unit.monitoring.index') }}" class="btn btn-outline-secondary w-100">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Tiket
            </a>
        </div>
    </div>
</div>
@endsection