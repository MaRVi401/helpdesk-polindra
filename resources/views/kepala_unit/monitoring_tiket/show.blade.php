@extends('layouts/contentNavbarLayout')
@use('Illuminate\Support\Str')
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
    .timeline-dot { position: absolute; left: -31px; top: 5px; width: 15px; height: 15px; border-radius: 50%; background-color: #4299e1; }
    .timeline-time { font-size: 0.85rem; color: #718096; margin-bottom: 0.25rem; }
    .timeline-title { font-weight: 600; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .timeline-body p { margin: 0; font-size: 0.95rem; }
    .timeline-body-comment { background-color: #f7fafc; border: 1px solid #e2e8f0; padding: 1rem; border-radius: 6px; }
    
    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; color: white; text-transform: capitalize; }
    
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
        {{-- KOLOM KIRI (Tindak Lanjut, Komentar, Riwayat) --}}
        <div class="col-md-8">
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tindak Lanjut</h5>
                    <span class="badge bg-label-primary">{{ $tiket->layanan->unit->nama_unit ?? '-' }}</span>
                </div>
                <div class="card-body">
                    @php
                        $currentStatus = $tiket->statusTerbaru?->status ?? '';
                        $canEdit = ($currentStatus === 'Pemohon_Bermasalah');
                    @endphp

                    @if(!$canEdit)
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="bx bx-lock-alt me-2"></i>
                            <div>
                                <strong>Menu Terkunci:</strong> Anda hanya dapat mengubah status jika tiket saat ini berstatus <strong>"Pemohon Bermasalah"</strong>. Saat ini status adalah: <em>{{ str_replace('_', ' ', $currentStatus ?: 'Baru') }}</em>.
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

            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Diskusi & Komentar</h5>
                </div>
                <div class="card-body pt-4">
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

            <div class="card mt-4">
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

        {{-- KOLOM KANAN (Sidebar) --}}
        <div class="col-md-4">
            
            {{-- 1. INFORMASI TIKET (POSISI PALING ATAS) --}}
            <div class="card mb-4">
                <div class="card-header bg-label-secondary text-dark fw-bold">
                    <i class="bx bx-info-circle me-1"></i> Informasi Tiket
                </div>
                <div class="card-body pt-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">No Tiket</dt>
                        <dd class="col-sm-8 text-end fw-bold">#{{ $tiket->no_tiket }}</dd>

                        <dt class="col-sm-4">Layanan</dt>
                        <dd class="col-sm-8 text-end">{{ $tiket->layanan->nama }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8 text-end">
                            @php
                                $st = $tiket->statusTerbaru?->status ?? 'Baru';
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
                            <span class="badge {{ $prioClassShow }}">{{ $prioLabelShow }}</span>
                        </dd>
                        
                        <hr class="my-3">
                        
                        <dt class="col-sm-12 mb-1">Deskripsi Awal:</dt>
                        <dd class="col-sm-12 bg-lighter p-2 rounded text-break">
                            {{ $tiket->deskripsi }}
                        </dd>
                    </dl>

                    @if(isset($detailLayanan))
                        {{-- Bagian Detail Layanan di View --}}
                @if(isset($detailLayanan))
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
                             
                             @if(isset($detailLayanan->gambar) && $detailLayanan->gambar)
                                <li class="mt-2">
                                    <span class="d-block fw-bold mb-1">Lampiran Gambar:</span>
                                    <div class="mb-2 text-center border rounded p-1 bg-light">
                                        <img src="{{ asset('storage/' . $detailLayanan->gambar) }}" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                    <a href="{{ asset('storage/' . $detailLayanan->gambar) }}" target="_blank" class="btn btn-xs btn-primary w-100">
                                        <i class='bx bx-image me-1'></i> Lihat Gambar Penuh
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>

            {{-- 2. FITUR TIMER (DIPINDAH KE BAWAH INFORMASI TIKET) --}}
            @php
                $cacheKey = 'tiket_timer_' . $tiket->id;
                $deadline = \Illuminate\Support\Facades\Cache::get($cacheKey);
                $isTimerActive = ($tiket->statusTerbaru?->status === 'Diselesaikan_oleh_PIC' && $deadline);
            @endphp

            @if($isTimerActive)
                <div class="card mb-4 border border-warning shadow-sm">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 text-white fw-bold"><i class='bx bx-timer'></i> Timer (Realtime)</h6>
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
                        var countDownDate = new Date(deadlineStr).getTime();

                        var x = setInterval(function() {
                            var now = new Date().getTime();
                            var distance = countDownDate - now;

                            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            document.getElementById("admin-countdown").innerHTML = 
                                days + "d " + hours + "h " + minutes + "m " + seconds + "s";

                            if (distance < 0) {
                                clearInterval(x);
                                document.getElementById("admin-countdown").innerHTML = "Memproses status...";
                                document.getElementById("admin-countdown").classList.remove('alert-warning');
                                document.getElementById("admin-countdown").classList.add('alert-success');
                                
                                setTimeout(function() {
                                    window.location.reload();
                                }, 3000);
                            }
                        }, 1000);
                    });
                </script>
            @endif

            {{-- 3. DATA PEMOHON --}}
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
@endif
@endif
@endsection