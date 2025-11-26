@use('Illuminate\Support\Str')
@extends('layouts/contentNavbarLayout')


@section('title', 'Detail Tiket #' . $tiket->no_tiket)

@section('content')
<style>
    /* --- CSS Status Badge --- */
    .status-badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; color: white; text-transform: capitalize; }
    .status-diajukan_oleh_pemohon { background-color: #a0aec0; color: #fff; } /* Abu-abu */
    .status-ditangani_oleh_pic, .status-diselesaikan_oleh_pic { background-color: #f6ad55; color: white; } /* Kuning/Orange */
    .status-dinilai_belum_selesai_oleh_pemohon, .status-pemohon_bermasalah, .status-ditolak { background-color: #f56565; color: white; } /* Merah */
    .status-dinilai_selesai_oleh_kepala, .status-dinilai_selesai_oleh_pemohon { background-color: #48bb78; color: white; } /* Hijau */

    /* --- Timeline & Chat Styles --- */
    .timeline { position: relative; padding-left: 1rem; }
    .timeline-item { position: relative; padding-left: 2rem; padding-bottom: 1.5rem; border-left: 2px solid #e2e8f0; }
    .timeline-item:last-child { border-left: 0; padding-bottom: 0; }
    .timeline-dot { position: absolute; left: -7px; top: 0; width: 16px; height: 16px; border-radius: 50%; border: 2px solid #fff; }
    .timeline-time { font-size: 0.75rem; color: #a0aec0; margin-bottom: 0.25rem; }
    .timeline-title { font-weight: 600; margin-bottom: 0.25rem; font-size: 0.9rem; }
    .timeline-body-comment { background-color: #f7fafc; border: 1px solid #e2e8f0; padding: 1rem; border-radius: 6px; margin-top: 0.5rem; }
    
    .dot-super_admin { background-color: #e53e3e !important; }
    .dot-mahasiswa { background-color: #4299e1 !important; }
    .dot-kepala_unit { background-color: #805ad5 !important; }
    .dot-admin_unit { background-color: #38a169 !important; }
    .dot-system { background-color: #718096 !important; }

    .bg-chat-me { background-color: #e7f3ff; border-color: #cce4ff; }
    .bg-chat-other { background-color: #ffffff; border-color: #e2e8f0; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin Unit /</span> Detail Tiket
    </h4>

    <div class="row">
        {{-- KOLOM KIRI --}}
        <div class="col-md-8">
            
            {{-- CARD 1: TINDAK LANJUT (FORM UPDATE STATUS) --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tindak Lanjut</h5>
                    <span class="badge bg-label-primary">{{ $tiket->layanan->unit->nama_unit ?? '-' }}</span>
                </div>
                <div class="card-body">
                    
                    {{-- Alert Informasi Status --}}
                    @if($isFormDisabled)
                        <div class="alert alert-secondary d-flex align-items-center mb-3" role="alert">
                            <i class="bx bx-time-five me-2"></i>
                            <div>{{ $statusMessage ?? 'Menunggu respon.' }}</div>
                        </div>
                    @elseif($tiket->statusTerbaru?->status == 'Dinilai_Belum_Selesai_oleh_Pemohon')
                        <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                            <i class="bx bx-error me-2"></i>
                            <div>
                                <strong>Perhatian:</strong> Mahasiswa menolak penyelesaian tiket ini. <br>
                                Total Penolakan: <strong>{{ $rejectionCount }}x</strong>.
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin_unit.tiket.update', $tiket->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Status Pengerjaan (Next Action)</label>
                                
                                <select name="status" class="form-select" {{ $isFormDisabled ? 'disabled' : '' }} required>
                                    @if($isFormDisabled)
                                        <option selected>{{ str_replace('_', ' ', $tiket->statusTerbaru?->status) }}</option>
                                    @else
                                        <option value="" selected disabled>-- Pilih Tindakan Selanjutnya --</option>
                                        @foreach($nextOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                @if(!$isFormDisabled)
                                    <div class="form-text text-primary">
                                        Pilih status berikutnya untuk memproses tiket ini.
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100" {{ $isFormDisabled ? 'disabled' : '' }}>
                                    <i class="bx bx-save me-1"></i> Simpan Status
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- CARD 2: RIWAYAT STATUS (TERPISAH) --}}
            <div class="card mb-4">
                <div class="card-header border-bottom bg-light py-3">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-history me-2"></i>Riwayat Status</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="timeline">
                        @forelse($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                            @php
                                $role = $riwayat->user->role ?? 'system';
                                $userName = $riwayat->user->name ?? 'Sistem';
                                $dotClass = 'dot-' . $role;
                                
                                $statusColor = match($riwayat->status) {
                                    'Diajukan_oleh_Pemohon' => '#718096',
                                    'Ditangani_oleh_PIC', 'Diselesaikan_oleh_PIC' => '#d69e2e',
                                    'Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon' => '#38a169',
                                    'Dinilai_Belum_Selesai_oleh_Pemohon', 'Pemohon_Bermasalah', 'Ditolak' => '#e53e3e',
                                    default => '#718096'
                                };
                            @endphp
                            <div class="timeline-item">
                                <span class="timeline-dot {{ $dotClass }}"></span>
                                <div class="timeline-time">{{ $riwayat->created_at->format('d M Y, H:i') }}</div>
                                <div class="timeline-title d-flex flex-wrap align-items-center gap-2">
                                    <span class="fw-bold">{{ $userName }}</span>
                                    @if($role !== 'system')
                                        <span class="badge bg-label-secondary" style="font-size: 0.7rem;">{{ str_replace('_', ' ', ucfirst($role)) }}</span>
                                    @endif
                                </div>
                                <div class="timeline-body mt-1">
                                    Mengubah status menjadi: 
                                    <strong style="color: {{ $statusColor }}">{{ str_replace('_', ' ', $riwayat->status) }}</strong>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted fst-italic mb-0">Belum ada riwayat status.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- CARD 3: DISKUSI & KOMENTAR (TERPISAH) --}}
            <div class="card mb-4">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-chat me-2"></i>Diskusi & Komentar</h5>
                    <span class="badge bg-label-primary">{{ $tiket->komentar->count() }} Pesan</span>
                </div>
                <div class="card-body pt-4">
                    <form action="{{ route('admin_unit.tiket.komentar', $tiket->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea name="komentar" class="form-control" rows="2" placeholder="Tulis pesan untuk pemohon atau catatan pengerjaan..." required {{ in_array($tiket->statusTerbaru?->status, ['Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon', 'Ditolak']) ? 'disabled' : '' }}></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-secondary" {{ in_array($tiket->statusTerbaru?->status, ['Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon', 'Ditolak']) ? 'disabled' : '' }}>
                                <i class="bx bx-send me-1"></i> Kirim Komentar
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="timeline">
                        @forelse($tiket->komentar->sortByDesc('created_at') as $komen)
                            @php
                                $role = $komen->pengirim->role ?? 'unknown';
                                $userName = $komen->pengirim->name;
                                $dotClass = 'dot-' . $role;
                                $isMe = $komen->pengirim_id == Auth::id();
                            @endphp
                            <div class="timeline-item">
                                <span class="timeline-dot {{ $dotClass }}"></span>
                                <div class="timeline-time">{{ $komen->created_at->format('d M Y, H:i') }}</div>
                                <div class="timeline-title">
                                    {{ $userName }} 
                                    <span class="text-muted fw-normal" style="font-size:0.8em">({{ str_replace('_', ' ', ucfirst($role)) }})</span>
                                </div>
                                <div class="timeline-body timeline-body-comment {{ $isMe ? 'bg-chat-me' : 'bg-chat-other' }}">
                                    <p class="mb-0 text-break">{!! nl2br(e($komen->komentar)) !!}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bx bx-message-rounded-dots fs-1 text-muted mb-2"></i>
                                <p class="text-muted">Belum ada diskusi pada tiket ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (INFO SIDEBAR) --}}
        <div class="col-md-4">
            
            {{-- 1. INFORMASI TIKET --}}
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
                                $prioLabelShow = match($prioVal) { 3 => 'Tinggi', 2 => 'Sedang', default => 'Rendah' };
                                $prioClassShow = match($prioVal) { 3 => 'prioritas-tinggi', 2 => 'prioritas-sedang', default => 'prioritas-rendah' };
                            @endphp
                            <span class="badge {{ $prioClassShow }}">{{ $prioLabelShow }}</span>
                        </dd>
                        
                        <hr class="my-3">
                        
                        <dt class="col-sm-12 mb-1">Deskripsi Awal:</dt>
                        <dd class="col-sm-12 bg-lighter p-2 rounded text-break">
                            {{ $tiket->deskripsi }}
                        </dd>
                    </dl>

                    {{-- Detail Layanan Spesifik --}}
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

            {{-- 2. TIMER (Untuk status Diselesaikan_oleh_PIC) --}}
            @php
                $cacheKey = 'tiket_timer_' . $tiket->id;
                $deadline = \Illuminate\Support\Facades\Cache::get($cacheKey);
                $isTimerActive = ($tiket->statusTerbaru?->status === 'Diselesaikan_oleh_PIC' && $deadline);
            @endphp

            @if($isTimerActive)
                <div class="card mb-4 border border-warning shadow-sm">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 text-white fw-bold"><i class='bx bx-timer'></i> Batas Konfirmasi</h6>
                    </div>
                    <div class="card-body pt-3 text-center">
                        <p class="mb-1 text-muted small">Auto-close pada:</p>
                        <h6 class="fw-bold text-dark mb-2">
                            {{ \Carbon\Carbon::parse($deadline)->format('d M Y, H:i:s') }}
                        </h6>
                        
                        <div id="admin-countdown" class="alert alert-warning p-2 mb-3 fw-bold" style="font-size: 1.1rem;">
                            Memuat...
                        </div>

                        <hr class="my-2">

                        <div class="collapse" id="settingTimer">
                            <form action="{{ route('admin_unit.tiket.updateTimer', $tiket->id) }}" method="POST" class="bg-light p-2 rounded border">
                                @csrf
                                @method('PUT')
                                <div class="input-group input-group-sm">
                                    <input type="number" name="amount" class="form-control" placeholder="Nilai" min="1" value="1" required>
                                    <select name="unit" class="form-select" style="max-width: 90px;">
                                        <option value="days">Hari</option>
                                        <option value="hours">Jam</option>
                                    </select>
                                    <button class="btn btn-primary" type="submit">Set</button>
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
                            document.getElementById("admin-countdown").innerHTML = days + "h " + hours + "j " + minutes + "m ";
                            if (distance < 0) {
                                clearInterval(x);
                                document.getElementById("admin-countdown").innerHTML = "Waktu Habis";
                            }
                        }, 1000);
                    });
                </script>
            @endif

            {{-- 3. DATA PEMOHON --}}
            <div class="card">
                <div class="card-header bg-label-primary text-white" style="background-color: #696cff; color:white !important;">
                   <i class="bx bx-user me-1"></i> Data Pemohon
                </div>
                <div class="card-body text-center pt-4">
                    <div class="avatar avatar-xl mx-auto mb-2">
                        @if($tiket->pemohon->profile_photo_path)
                            <img src="{{ asset('storage/' . $tiket->pemohon->profile_photo_path) }}" class="rounded-circle" alt="Avatar">
                        @else
                            <span class="avatar-initial rounded-circle bg-label-info">
                                {{ strtoupper(substr($tiket->pemohon->name ?? 'U', 0, 2)) }}
                            </span>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ $tiket->pemohon->name }}</h5>
                    <p class="text-muted mb-0 small">{{ $tiket->pemohon->email }}</p>
                    @if($tiket->pemohon->mahasiswa)
                        <hr>
                        <div class="text-start small">
                            <div><strong>NIM:</strong> {{ $tiket->pemohon->mahasiswa->nim }}</div>
                            <div><strong>Prodi:</strong> {{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? '-' }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('admin_unit.tiket.index') }}" class="btn btn-outline-secondary w-100">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection