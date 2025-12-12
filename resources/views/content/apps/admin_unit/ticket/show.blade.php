@use('Illuminate\Support\Str')
@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Tiket #' . $tiket->no_tiket)

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/management/service-ticket/show-admin.js')
@endsection

@section('content')
<<<<<<< HEAD

    <style>
        /* --- CSS Status Badge & Prioritas (Disempurnakan) --- */
        .status-badge,
        .prioritas-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
            line-height: 1.2;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .status-diajukan_oleh_pemohon {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .status-ditangani_oleh_pic {
            background-color: #f6ad55;
            color: white;
        }

        .status-diselesaikan_oleh_pic {
            background-color: #4299e1;
            color: white;
        }

        .status-dinilai_belum_selesai_oleh_pemohon,
        .status-pemohon_bermasalah,
        .status-ditolak {
            background-color: #f56565;
            color: white;
        }

        .status-dinilai_selesai_oleh_kepala,
        .status-dinilai_selesai_oleh_pemohon {
            background-color: #48bb78;
            color: white;
        }

        .prioritas-rendah {
            background-color: #a0aec0;
            color: white;
        }

        .prioritas-sedang {
            background-color: #48bb78;
            color: white;
        }

        .prioritas-tinggi {
            background-color: #f56565;
            color: white;
        }

        /* --- Timeline Style (Disesuaikan) --- */
        .timeline {
            position: relative;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            padding-left: 20px;
            padding-bottom: 25px;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background-color: #e2e8f0;
            margin-left: -2px;
        }

        .timeline-dot {
            position: absolute;
            left: -8px;
            top: 3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #fff;
            z-index: 10;
        }

        .timeline-time {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 0.25rem;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Dot dan Body Komentar berdasarkan Role */
        .dot-mahasiswa {
            background-color: #4299e1 !important;
        }

        .body-mahasiswa {
            background-color: #ebf8ff !important;
            border-color: #bee3f8 !important;
        }

        .dot-admin_unit {
            background-color: #38a169 !important;
        }

        .body-admin_unit {
            background-color: #f0fff4 !important;
            border-color: #c6f6d5 !important;
        }

        .dot-kepala_unit {
            background-color: #805ad5 !important;
        }

        .body-kepala_unit {
            background-color: #faf5ff !important;
            border-color: #e9d8fd !important;
        }

        .dot-super_admin {
            background-color: #e53e3e !important;
        }

        .body-super_admin {
            background-color: #fef2f2 !important;
            border-color: #fbd7d7 !important;
        }

        .dot-system {
            background-color: #a0aec0 !important;
        }

        .timeline-body-comment {
            border: 1px solid;
            padding: 1rem;
            border-radius: 6px;
        }

        /* Grid untuk Detail Layanan (Sidebar) */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 5px 15px;
        }

        .info-grid dd,
        .info-grid dt {
            margin: 0;
            padding: 0;
        }

        .info-grid dt {
            font-weight: 600;
            color: #4a5568;
            margin-top: 5px;
        }

        .info-grid dd {
            margin-bottom: 5px;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin Unit / Tiket /</span> Detail Tiket
        </h4>

        {{-- Deklarasi PHP Awal untuk data yang sering digunakan --}}
        @php
            $currentStatus = $tiket->statusTerbaru?->status ?? 'Diajukan_oleh_Pemohon';
            $currentStatusLabel = str_replace('_', ' ', $currentStatus);

            // Logic Prioritas
            $prioVal = $tiket->layanan->prioritas ?? 2;
            $prioLabel = match ($prioVal) {
                3 => 'Tinggi',
                2 => 'Sedang',
                default => 'Rendah',
            };
            $prioClass = 'prioritas-' . strtolower($prioLabel);

            // Logic Status Class
            $statusClass = 'status-' . strtolower($currentStatus);

            // Logic PIC (Diambil dari riwayat Ditangani_oleh_PIC terakhir)
            $picStatus =
                $tiket->riwayatStatus->where('status', 'Ditangani_oleh_PIC')->last() ??
                $tiket->riwayatStatus->whereIn('status', ['Diselesaikan_oleh_PIC', 'Pemohon_Bermasalah'])->last();
            $picUser = $picStatus?->user;
            $picName = $picUser ? $picUser->name : 'Menunggu penanganan...';

            // Logic Tindak Lanjut Admin Unit (dari controller show)
            $isFormDisabled = $isFormDisabled ?? false;
            $statusMessage = $statusMessage ?? '';
        @endphp

        <div class="row g-4">

            {{-- KOLOM KIRI (Tindakan, Deskripsi, Riwayat) - ORDER 2 di mobile, 1 di desktop --}}
            <div class="col-lg-8 col-md-12 order-lg-1 order-2">

                {{-- 1. TINDAKAN TIKET LAYANAN (Admin Unit Action Card) --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Tindakan Tiket Layanan</h5>
                        <span
                            class="badge bg-label-primary">{{ $tiket->layanan->unit->nama_unit ?? 'Unit Tidak Ditemukan' }}</span>
                    </div>
                    <div class="card-body">

                        {{-- Alert Informasi Status --}}
                        @if ($isFormDisabled)
                            @php
                                $alertClass =
                                    $currentStatus === 'Diselesaikan_oleh_PIC' ? 'alert-info' : 'alert-success';
                                $iconClass =
                                    $currentStatus === 'Diselesaikan_oleh_PIC' ? 'ti-info-circle' : 'ti-circle-check';
                            @endphp
                            <div class="alert {{ $alertClass }} d-flex align-items-center mb-3" role="alert">
                                <i class="ti {{ $iconClass }} me-2"></i>
                                <div>{{ $statusMessage ?? 'Menunggu respon...' }}</div>
                            </div>
                        @elseif($currentStatus == 'Dinilai_Belum_Selesai_oleh_Pemohon')
                            <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <div>
                                    <strong>Perhatian:</strong> Pemohon menolak penyelesaian tiket ini.<br>
                                    Total Penolakan: <strong>{{ $rejectionCount ?? 0 }}x</strong>.
                                </div>
                            </div>
                        @endif

                        <form id="form-update-status" action="{{ route('admin_unit.ticket.update', $tiket->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Status Pengerjaan</label>

                                    <select name="status" class="form-select" {{ $isFormDisabled ? 'disabled' : '' }}
                                        required>
                                        @if ($isFormDisabled)
                                            <option selected>{{ $currentStatusLabel }}</option>
                                        @else
                                            <option value="" selected disabled>Pilih Tindakan Selanjutnya</option>
                                            @foreach ($nextOptions ?? [] as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                    @if (!$isFormDisabled)
                                        <div class="form-text text-warning">Pilih status berikutnya untuk memproses tiket
                                            ini.</div>
                                    @endif
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary w-100"
                                        {{ $isFormDisabled ? 'disabled' : '' }}>
                                        <i class="bx bx-save me-1"></i> Simpan Status
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 2. DESKRIPSI AWAL (Dipindahkan dari Sidebar) --}}
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="mb-0">Deskripsi Awal Keluhan</h6>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-break">{{ $tiket->deskripsi ?? '-' }}</p>
                    </div>
                </div>

                {{-- 3. RIWAYAT KOMENTAR --}}
                <div class="card mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="mb-0">Komentar</h5>
                    </div>
                    <div class="card-body">

                        {{-- Form Komentar --}}
                        @php
                            $isCommentDisabled = in_array($currentStatus, [
                                'Dinilai_Selesai_oleh_Kepala',
                                'Dinilai_Selesai_oleh_Pemohon',
                                'Ditolak',
                            ]);
                        @endphp
                        <form action="{{ route('admin_unit.ticket.comment', $tiket->id) }}" method="POST" class="mb-5">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold"></label>
                                <textarea name="komentar" class="form-control" rows="3"
                                    placeholder="Tulis pesan untuk pemohon atau catatan internal..." required
                                    {{ $isCommentDisabled ? 'disabled' : '' }}></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-secondary"
                                    {{ $isCommentDisabled ? 'disabled' : '' }}>
                                    <i class="bx bx-send me-1"></i> Kirim Komentar
                                </button>
                            </div>
                        </form>

                        {{-- Riwayat Komentar --}}
                        <h6 class="mb-3 text-muted border-bottom pb-2">Riwayat Komentar ({{ $tiket->komentar->count() }})
                        </h6>
                        <div class="timeline">
                            @forelse($tiket->komentar->sortBy('created_at') as $komen)
                                @php
                                    $role = $komen->pengirim->role ?? 'system';
                                    $dotClass = 'dot-' . Str::slug($role, '_');
                                    $bodyClass = 'body-' . Str::slug($role, '_');
                                    $userName = $komen->pengirim->name ?? 'Pengguna Dihapus';
                                @endphp
                                <div class="timeline-item">
                                    <span class="timeline-dot {{ $dotClass }}"></span>
                                    <div class="timeline-content">
                                        <div class="timeline-time">{{ $komen->created_at->translatedFormat('d F Y, H:i') }}
                                        </div>
                                        <div class="timeline-title">
                                            {{ $userName }}
                                            (<strong class="text-primary"
                                                style="text-transform: capitalize;">{{ str_replace('_', ' ', $role) }}</strong>)
                                        </div>
                                        <div class="timeline-body timeline-body-comment {{ $bodyClass }}">
                                            <p class="m-0 text-break">{!! nl2br(e($komen->komentar)) !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted">Belum ada komentar dalam diskusi ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- 4. RIWAYAT STATUS --}}
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Riwayat Status</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="timeline">
                            @forelse ($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                                @php
                                    $role = $riwayat->user->role ?? 'system';
                                    $userName = $riwayat->user->name ?? 'Sistem Otomatis';
                                    $dotRoleClass = 'dot-' . Str::slug($role, '_');

                                    $statusSlug = Str::slug($riwayat->status, '_');
                                    $statusClassRiwayat = 'status-' . strtolower($statusSlug);
                                @endphp
                                <div class="timeline-item">
                                    <span class="timeline-dot {{ $dotRoleClass }}"></span>
                                    <div class="timeline-content">
                                        <div class="timeline-time">
                                            {{ $riwayat->created_at->translatedFormat('d F Y, H:i') }}</div>
                                        <div class="timeline-title">
                                            <span class="fw-bold">{{ $userName }}</span>
                                            @if ($role !== 'system')
                                                <span style="font-size:0.8em">
                                                    ({{ ucwords(strtolower(str_replace('_', ' ', trim($role)))) }})
                                                </span>
                                            @endif
                                        </div>
                                        <div class="timeline-body">
                                            Status Layanan diubah menjadi:
                                            <span class="status-badge {{ $statusClassRiwayat }}">
                                                {{ str_replace('_', ' ', $riwayat->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted mb-0">Belum ada riwayat status.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (Sidebar) - ORDER 1 di mobile, 2 di desktop --}}
            <div class="col-lg-4 col-md-12 order-lg-2 order-1">

                {{-- 1. INFORMASI TIKET --}}
                <div class="card mb-4">
                    <div class="card-header bg-label-info text-dark fw-bold py-3">
                        <h6 class="mb-0"><i class="bx bx-ticket me-1"></i> Detail Utama Tiket</h6>
                    </div>
                    <div class="card-body pt-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">No Tiket</dt>
                            <dd class="col-sm-8 text-end fw-bold text-primary">{{ $tiket->no_tiket }}</dd>

                            <dt class="col-sm-4 text-muted">Layanan</dt>
                            <dd class="col-sm-8 text-end text-wrap">{{ $tiket->layanan->nama }}</dd>

                            <dt class="col-sm-4 text-muted">Unit</dt>
                            <dd class="col-sm-8 text-end">{{ $tiket->layanan->unit->nama_unit ?? '-' }}</dd>

                            <hr class="my-2">

                            <dt class="col-sm-4 text-muted">Status</dt>
                            <dd class="col-sm-8 text-end">
                                <span class="status-badge {{ $statusClass }}">{{ $currentStatusLabel }}</span>
                            </dd>

                            <dt class="col-sm-4 text-muted mt-2">Prioritas</dt>
                            <dd class="col-sm-8 mt-2 text-end">
                                <span class="prioritas-badge {{ $prioClass }}">{{ $prioLabel }}</span>
                            </dd>

                            <hr class="my-2">

                            <dt class="col-sm-4 text-muted">PIC</dt>
                            <dd class="col-sm-8 text-end fw-bold">{{ $picName }}</dd>

                            <dt class="col-sm-4 text-muted">Dibuat</dt>
                            <dd class="col-sm-8 text-end">{{ $tiket->created_at->translatedFormat('d F Y') }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- 2. DATA PEMOHON --}}
                <div class="card mb-4">
                    <div class="card-header bg-label-secondary text-dark fw-bold py-3">
                        <h6 class="mb-0"><i class="bx bx-user-circle me-1"></i> Data Pemohon</h6>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $pemohon = $tiket->pemohon ?? null;
                            $avatarText = strtoupper(substr($pemohon->name ?? 'U', 0, 2));
                        @endphp
                        <div class="avatar avatar-xl mx-auto mb-2">
                            <span class="avatar-initial rounded-circle bg-label-info">
                                {{ $avatarText }}
                            </span>
                        </div>
                        <h5 class="mb-1">{{ $pemohon->name ?? 'Pengguna Dihapus' }}</h5>
                        <p class="text-muted mb-0 small">{{ $pemohon->email ?? '-' }}</p>

                        @if ($pemohon && $pemohon->mahasiswa)
                            <hr class="my-3">
                            <div class="text-start small">
                                <dl class="row mb-0">
                                    <dt class="col-4 text-muted">NIM:</dt>
                                    <dd class="col-8 text-end fw-bold">{{ $tiket->pemohon->mahasiswa->nim ?? '-' }}</dd>
                                    <dt class="col-4 text-muted">Prodi:</dt>
                                    <dd class="col-8 text-end">
                                        {{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? '-' }}</dd>
                                </dl>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 3. FITUR TIMER (Jika Aktif) --}}
                @php
                    $cacheKey = 'tiket_timer_' . $tiket->id;
                    $deadline = \Illuminate\Support\Facades\Cache::get($cacheKey);
                    $isTimerActive = $currentStatus === 'Diselesaikan_oleh_PIC' && $deadline;
                @endphp

                @if ($isTimerActive)
                    <div class="card mb-4 border border-warning shadow-sm">
                        <div
                            class="card-header bg-warning text-white d-flex justify-content-between align-items-center py-2">
                            <h6 class="mb-0 text-white fw-bold"><i class='bx bx-timer'></i> Timer Validasi Pemohon</h6>
                        </div>
                        <div class="card-body pt-3 text-center">
                            <p class="mb-1 text-muted small">Otomatis selesai pada:</p>
                            <h6 class="fw-bold text-dark mb-2">
                                {{ \Carbon\Carbon::parse($deadline)->format('d M Y, H:i:s') }}
                            </h6>

                            <div id="admin-countdown" class="alert alert-warning p-2 mb-3 fw-bold"
                                data-deadline="{{ \Carbon\Carbon::parse($deadline)->format('Y-m-d H:i:s') }}"
                                style="font-size: 1.2rem;">
                                Memuat...
                            </div>

                            <hr class="my-2">
                            <div class="collapse" id="settingTimer">
                                <form action="{{ route('admin_unit.ticket.updateTimer', $tiket->id) }}" method="POST"
                                    class="bg-light p-2 rounded border">
                                    @csrf
                                    @method('PUT')
                                    <label class="form-label small text-start w-100 fw-bold">Set Durasi Baru</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="amount" class="form-control" placeholder="Angka"
                                            min="1" required>

                                        <select name="unit" class="form-select" style="max-width: 100px;">
                                            <option value="days">Hari</option>
                                            <option value="hours">Jam</option>
                                            <option value="minutes">Menit</option>
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

                    {{-- Skrip Timer (Tetap di sini) --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var countdownElement = document.getElementById("admin-countdown");
                            if (!countdownElement) return;

                            var deadlineStr = countdownElement.getAttribute('data-deadline');
                            if (!deadlineStr) return;

                            var countDownDate = new Date(deadlineStr.replace(' ', 'T')).getTime();

                            var x = setInterval(function() {
                                var now = new Date().getTime();
                                var distance = countDownDate - now;

                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                var pad = (num) => num.toString().padStart(2, '0');

                                var countdownText = "";
                                if (days > 0) countdownText += days + "h ";
                                countdownText += pad(hours) + "j " + pad(minutes) + "m " + pad(seconds) + "d";

                                countdownElement.innerHTML = countdownText;

                                if (distance < 0) {
                                    clearInterval(x);
                                    countdownElement.innerHTML = "Waktu Habis. Memproses status...";
                                    countdownElement.classList.remove('alert-warning');
                                    countdownElement.classList.add('alert-success');

                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 3000);
                                }
                            }, 1000);
                        });
                    </script>
                @endif

                {{-- 5. DETAIL LAYANAN SPESIFIK (Dipindahkan ke bawah sidebar) --}}
                @if (isset($detailLayanan))
                    <div class="card mb-4">
                        <div class="card-header bg-label-primary text-dark fw-bold py-3">
                            <h6 class="mb-0"><i class="bx bx-file-text me-1"></i> Detail Formulir Layanan</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3 text-primary">{{ $tiket->layanan->nama }}</h6>
                            <div class="info-grid">

                                {{-- 1. Surat Keterangan Aktif Kuliah --}}
                                @if (Str::contains($tiket->layanan->nama, 'Surat Keterangan Aktif Kuliah'))
                                    <dt>Keperluan</dt>
                                    <dd class="text-break">{{ $detailLayanan->keperluan ?? '-' }}</dd>
                                    <dt>Tahun Ajaran</dt>
                                    <dd>{{ $detailLayanan->tahun_ajaran ?? '-' }}</dd>
                                    <dt>Semester</dt>
                                    <dd>{{ $detailLayanan->semester ?? '-' }}</dd>
                                    @if ($detailLayanan->keperluan_lainnya ?? false)
                                        <dt class="col-12 border-top pt-2">Keperluan Lainnya</dt>
                                        <dd class="col-12 border-top pt-2 text-break">
                                            {{ $detailLayanan->keperluan_lainnya }}</dd>
                                    @endif

                                    {{-- 2. Reset Akun --}}
                                @elseif(Str::contains($tiket->layanan->nama, 'Reset Akun'))
                                    <dt>Aplikasi</dt>
                                    <dd>{{ $detailLayanan->aplikasi ?? '-' }}</dd>
                                    <dt>Deskripsi Masalah</dt>
                                    <dd class="text-break">{{ $detailLayanan->deskripsi ?? '-' }}</dd>

                                    {{-- 3. Ubah Data Mahasiswa --}}
                                @elseif(Str::contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                                    <dt>Nama Lengkap</dt>
                                    <dd>{{ $detailLayanan->data_nama_lengkap ?? '-' }}</dd>
                                    <dt>Tempat Lahir Baru</dt>
                                    <dd>{{ $detailLayanan->data_tmp_lahir ?? '-' }}</dd>
                                    <dt>Tanggal Lahir Baru</dt>
                                    <dd>{{ $detailLayanan->data_tgl_lhr ? \Carbon\Carbon::parse($detailLayanan->data_tgl_lhr)->format('d-m-Y') : '-' }}
                                    </dd>

                                    {{-- 4. Request Publikasi --}}
                                @elseif(Str::contains($tiket->layanan->nama, 'Request Publikasi') || Str::contains($tiket->layanan->nama, 'Publikasi'))
                                    <dt>Judul / Topik</dt>
                                    <dd class="text-break">{{ $detailLayanan->judul ?? '-' }}</dd>
                                    <dt>Kategori</dt>
                                    <dd>{{ $detailLayanan->kategori ?? '-' }}</dd>

                                    <div class="col-12 border-top pt-2">
                                        <span class="d-block fw-bold text-muted mb-1">Konten / Isi:</span>
                                        <small>{!! nl2br(e($detailLayanan->konten ?? '-')) !!}</small>
                                    </div>

                                    @if (isset($detailLayanan->gambar) && $detailLayanan->gambar)
                                        <div class="col-12">
                                            <hr class="my-3">
                                            <span class="d-block fw-bold mb-1">Lampiran Gambar:</span>
                                            <div class="mb-2 text-center border rounded p-1 bg-light">
                                                <img src="{{ asset('storage/' . $detailLayanan->gambar) }}"
                                                    alt="Preview" class="img-fluid rounded"
                                                    style="max-height: 150px; object-fit: cover; width: 100%;">
                                            </div>
                                            <a href="{{ asset('storage/' . $detailLayanan->gambar) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary w-100">
                                                <i class='bx bx-image me-1'></i> Lihat Gambar Penuh
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- TOMBOL KEMBALI --}}
                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ route('admin_unit.ticket.index') }}" class="btn btn-secondary w-100">
                                <i class="bx bx-chevron-left me-1"></i> Kembali ke Daftar Tiket Layanan
                            </a>
                        </div>
                    </div>
                @endif

            </div>
=======
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- TIMER --}}
    @php
      $cacheKey = 'tiket_timer_' . $tiket->id;
      $deadline = \Illuminate\Support\Facades\Cache::get($cacheKey);
      $isTimerActive = $tiket->statusTerbaru?->status === 'Diselesaikan_oleh_PIC' && $deadline;
    @endphp
    @if ($isTimerActive)
      <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
          <h6 class="mb-0 text-white">Batas Konfirmasi</h6>
>>>>>>> 33b9ec0 (get:student-article)
        </div>
        <div class="card-body pt-3 text-center">
          <p class="mb-1 text-warning small">Tiket ini akan otomatis ditutup jika tidak ada respon dalam</p>
          <hr class="my-2">
          <div id="admin-countdown"
            data-deadline="{{ $deadline ? \Carbon\Carbon::parse($deadline)->format('Y-m-d H:i:s') : '' }}"
            class="p-1 mb-1 fw-bold fs-6">
            Memuat...
          </div>
          <small class="text-muted mb-2">
            {{ $deadline ? \Carbon\Carbon::parse($deadline)->translatedFormat('d F Y, H:i:s') : '-' }}
          </small>
          <hr class="my-2">
          <div class="collapse" id="settingTimer">
            <form action="{{ route('admin_unit.ticket.updateTimer', $tiket->id) }}" method="POST"
              class="bg-light p-2 rounded border">
              @csrf
              @method('PUT')
              <div class="input-group input-group-sm">
                <input type="number" name="amount" class="form-control" placeholder="Nilai" min="1"
                  value="1" required>
                <select name="unit" class="form-select">
                  <option value="days">Hari</option>
                  <option value="hours">Jam</option>
                </select>
                <button class="btn btn-primary" type="submit">Set</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif

    <div class="row">
      {{-- KOLOM KIRI --}}
      <div class="col-md-8">
        {{-- TINDAK LANJUT --}}
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tindakan Tiket Layanan</h5>
            <span class="badge bg-label-primary">{{ $tiket->layanan->unit->nama_unit ?? '-' }}</span>
          </div>
          <div class="card-body">
            {{-- Alert Informasi Status --}}
            @if ($isFormDisabled && $tiket->statusTerbaru?->status === 'Diselesaikan_oleh_PIC')
              <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                <div>{{ $statusMessage ?? 'Menunggu respon...' }}</div>
              </div>
            @elseif ($isFormDisabled)
              <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                <i class="icon-base ti tabler-circle-check me-2"></i>
                <div>{{ $statusMessage ?? 'Menunggu respon...' }}</div>
              </div>
            @elseif($tiket->statusTerbaru?->status == 'Dinilai_Belum_Selesai_oleh_Pemohon')
              <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                <div>
                  <strong>Perhatian:</strong> Mahasiswa menolak penyelesaian tiket ini<br>
                  Total Penolakan: <strong>{{ $rejectionCount }}x</strong>.
                </div>
              </div>
            @endif
            <form id="form-update-status" action="{{ route('admin_unit.ticket.update', $tiket->id) }}" method="POST">
              @csrf
              @method('PUT')
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Status Pengerjaan</label>
                  @if (!$isFormDisabled)
                    <div class="form-text text-warning">
                      Pilih status berikutnya untuk memproses tiket ini.
                    </div>
                  @endif
                  <select name="status" class="form-select" {{ $isFormDisabled ? 'disabled' : '' }} required>
                    @if ($isFormDisabled)
                      <option selected>{{ str_replace('_', ' ', $tiket->statusTerbaru?->status) }}</option>
                    @else
                      <option value="" selected disabled>Pilih Tindakan Selanjutnya</option>
                      @foreach ($nextOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                      @endforeach
                    @endif
                  </select>
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

        {{-- KOMENTAR --}}
        <div class="card mb-4">
          <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Komentar Tiket</h5>
            <span class="badge bg-label-secondary">{{ $tiket->komentar->count() }} Komentar</span>
          </div>
          <div class="card-body pt-4">
            <form action="{{ route('admin_unit.ticket.comment', $tiket->id) }}" method="POST" class="mb-4">
              @csrf
              <div class="mb-3">
                <textarea name="komentar" class="form-control" rows="2"
                  placeholder="Tulis pesan untuk pemohon atau catatan pengerjaan..." required
                  {{ in_array($tiket->statusTerbaru?->status, ['Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon', 'Ditolak']) ? 'disabled' : '' }}></textarea>
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-primary"
                  {{ in_array($tiket->statusTerbaru?->status, ['Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon', 'Ditolak']) ? 'disabled' : '' }}>
                  Kirim Komentar
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
                  <div class="timeline-time">{{ $komen->created_at->translatedFormat('d F Y, H:i') }}</div>
                  <div class="timeline-title">
                    {{ $userName }}
                    <span style="font-size:0.8em">({{ ucwords(strtolower(str_replace('_', ' ', trim($role)))) }})</span>
                  </div>
                  <div class="border timeline-body-comment rounded-3 p-2">
                    <p class="mb-0 text-break">{!! nl2br(e($komen->komentar)) !!}</p>
                  </div>
                </div>
              @empty
                <div class="text-center py-5">
                  <i class="bx bx-message-rounded-dots fs-1 text-muted mb-2"></i>
                  <p class="text-muted">Belum ada komentar pada tiket ini.</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>

        {{-- RIWAYAT STATUS --}}
        <div class="card mb-4">
          <div class="card-header border-bottom py-3">
            <h5 class="mb-0">Riwayat Status</h5>
          </div>
          <div class="card-body pt-4">
            <div class="timeline">
              @forelse($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                @php
                  $role = $riwayat->user->role ?? 'system';
                  $userName = $riwayat->user->name ?? 'Sistem';
                  $dotClass = 'dot-' . $role;

                  $statusClass = match ($riwayat->status) {
                      'Diajukan_oleh_Pemohon' => 'bg-label-status-pending',
                      'Ditangani_oleh_PIC' => 'bg-label-status-process',
                      'Diselesaikan_oleh_PIC' => 'bg-label-status-review',
                      'Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon' => 'bg-label-status-completed',
                      'Dinilai_Belum_Selesai_oleh_Pemohon',
                      'Pemohon_Bermasalah',
                      'Ditolak'
                          => 'bg-label-status-rejected',
                      default => 'text-muted',
                  };
                @endphp
                <div class="timeline-item">
                  <span class="timeline-dot {{ $dotClass }}"></span>
                  <div class="timeline-time">
                    {{ $riwayat->created_at->translatedFormat('d F Y, H:i') }}
                  </div>
                  <div class="timeline-title d-flex flex-wrap align-items-center gap-2">
                    <span class="fw-bold">{{ $userName }}</span>
                    @if ($role !== 'system')
                      <span style="font-size:0.8em">
                        ({{ ucwords(strtolower(str_replace('_', ' ', trim($role)))) }})
                      </span>
                    @endif
                  </div>
                  <div class="timeline-body mt-1">
                    Status Layanan :
                    <span class="badge {{ $statusClass }}">
                      {{ str_replace('_', ' ', $riwayat->status) }}
                    </span>
                  </div>
                </div>
              @empty
                <p class="text-center text-muted  mb-0">Belum ada riwayat status.</p>
              @endforelse
            </div>
          </div>
        </div>
      </div>
      {{-- KOLOM KANAN --}}
      <div class="col-md-4">
        {{-- DATA PEMOHON --}}
        <div class="card mb-4">
          <div class="card-header border-bottom py-3">
            <h5 class="mb-0">Data Pemohon</h5>
          </div>
          <div class="card-body text-center pt-4">
            @php
              $pemohon = $tiket->pemohon ?? null;
              $avatarUrl = null;
              if ($pemohon) {
                  $avatarUrl = $pemohon->avatar
                      ? asset('storage/avatar/' . $pemohon->avatar)
                      : $pemohon->profile_photo_url ?? null;
              }
            @endphp
            <div class="avatar avatar-xl mx-auto mb-2 text-center">
              @if (!empty($avatarUrl))
                <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle" />
              @else
                <span
                  class="avatar-initial rounded-circle bg-label-info d-inline-flex align-items-center justify-content-center">
                  {{ strtoupper(substr($pemohon->name ?? 'U', 0, 2)) }}
                </span>
              @endif
            </div>
            <h5 class="mb-1">{{ $tiket->pemohon->name }}</h5>
            <p class="text-muted mb-0 small">{{ $tiket->pemohon->email }}</p>
            @if ($tiket->pemohon->mahasiswa)
              <hr>
              <div class="text-start small">
                <div> <span>NIM - {{ $tiket->pemohon->mahasiswa->nim }}</span></div>
                <div> <span>{{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? '-' }}</span>
                </div>
              </div>
            @endif
          </div>
        </div>
        {{-- INFORMASI TIKET --}}
        <div class="card mb-4">
          <div class="card-header border-bottom py-3">
            <h5 class="mb-0">Informasi Tiket</h5>
          </div>
          <div class="card-body pt-3 pb-3">
            <div class="mb-3">
              <span class="text-muted d-block">Nama Layanan</span>
              <small class="mb-0">{{ $tiket->layanan->nama }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">No Tiket</span>
              <small class="mb-0">{{ $tiket->no_tiket }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Prioritas</span>
              @php
                $prioVal = $tiket->layanan->prioritas ?? 2;
                $prioBadge = match ($prioVal) {
                    3 => 'bg-label-danger',
                    2 => 'bg-label-warning',
                    default => 'bg-label-info',
                };
                $prioLabel = match ($prioVal) {
                    3 => 'Tinggi',
                    2 => 'Sedang',
                    default => 'Rendah',
                };
              @endphp
              <span class="badge {{ $prioBadge }}">{{ $prioLabel }}</span>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Status</span>
              @php
                $currentStatus =
                    $tiket->riwayatStatus->sortByDesc('created_at')->first()?->status ??
                    ($tiket->statusTerbaru?->status ?? 'Diajukan_oleh_Pemohon');
                $statusClass = match ($currentStatus) {
                    'Diajukan_oleh_Pemohon' => 'bg-label-status-pending',
                    'Ditangani_oleh_PIC' => 'bg-label-status-process',
                    'Diselesaikan_oleh_PIC' => 'bg-label-status-review',
                    'Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon' => 'bg-label-status-completed',
                    'Dinilai_Belum_Selesai_oleh_Pemohon', 'Pemohon_Bermasalah', 'Ditolak' => 'bg-label-status-rejected',
                    default => 'bg-label-secondary',
                };
                $labelStatus = str_replace('_', ' ', $currentStatus);
              @endphp
              <small class="badge {{ $statusClass }}">{{ $labelStatus }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Penanggungjawab (PIC)</span>
              @php
                $picUser = $tiket->riwayatStatus->where('status', 'Ditangani_oleh_PIC')->first()?->user;
                $picName = $picUser ? $picUser->name : 'Menunggu penanganan...';
              @endphp
              <small>{{ $picName }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Dibuat pada</span>
              <small class="mb-0 small">{{ $tiket->created_at->translatedFormat('d F Y H:i') }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Deskripsi</span>
              <small class="mb-0 small">{{ $tiket->deskripsi }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- DETAIL LAYANAN SPESIFIK (FULL WIDTH) --}}
    @if (isset($detailLayanan))
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header border-bottom py-3">
              <h5 class="mb-0">Detail Layanan</h5>
            </div>
            <div class="card-body pt-3 pb-3">
              {{-- 1. Surat Keterangan Aktif Kuliah --}}
              @if (Str::contains($tiket->layanan->nama, 'Surat Keterangan Aktif Kuliah'))
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <span class="text-muted d-block">Keperluan</span>
                    <small class="mb-0">{{ $detailLayanan->keperluan }}</small>
                  </div>
                  <div class="col-md-3 mb-3">
                    <span class="text-muted d-block">Tahun Ajaran</span>
                    <small class="mb-0">{{ $detailLayanan->tahun_ajaran }}</small>
                  </div>
                  <div class="col-md-3 mb-3">
                    <span class="text-muted d-block">Semester</span>
                    <small class="mb-0">{{ $detailLayanan->semester }}</small>
                  </div>
                  @if ($detailLayanan->keperluan_lainnya)
                    <div class="col-12">
                      <hr class="my-2">
                      <span class="text-muted d-block">Keperluan Lainnya</span>
                      <small class="mb-0">{{ $detailLayanan->keperluan_lainnya }}</small>
                    </div>
                  @endif
                </div>

                {{-- 2. Reset Akun --}}
              @elseif(Str::contains($tiket->layanan->nama, 'Reset Akun'))
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <span class="text-muted d-block">Aplikasi</span>
                    <small class="mb-0">{{ $detailLayanan->aplikasi }}</small>
                  </div>
                  <div class="col-md-8 mb-3">
                    <span class="text-muted d-block">Deskripsi Masalah</span>
                    <small class="mb-0">{{ $detailLayanan->deskripsi }}</small>
                  </div>
                </div>

                {{-- 3. Ubah Data Mahasiswa --}}
              @elseif(Str::contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <span class="text-muted d-block">Nama Lengkap</span>
                    <small class="mb-0">{{ $detailLayanan->data_nama_lengkap ?? '-' }}</small>
                  </div>
                  <div class="col-md-4 mb-3">
                    <span class="text-muted d-block">Tempat Lahir</span>
                    <small class="mb-0">{{ $detailLayanan->data_tmp_lahir ?? '-' }}</small>
                  </div>
                  <div class="col-md-4 mb-3">
                    <span class="text-muted d-block">Tanggal Lahir</span>
                    <small
                      class="mb-0">{{ $detailLayanan->data_tgl_lhr ? date('d-m-Y', strtotime($detailLayanan->data_tgl_lhr)) : '-' }}</small>
                  </div>
                </div>

                {{-- 4. Request Publikasi --}}
              @elseif(Str::contains($tiket->layanan->nama, 'Request Publikasi') || Str::contains($tiket->layanan->nama, 'Publikasi'))
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <span class="text-muted d-block">Judul / Topik</span>
                    <small class="mb-0">{{ $detailLayanan->judul }}</small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <span class="text-muted d-block">Kategori</span>
                    <small class="mb-0">{{ $detailLayanan->kategori }}</small>
                  </div>
                  <div class="col-12 mb-3">
                    <hr class="my-2">
                    <span class="text-muted d-block">Konten / Isi</span>
                    <small class="mb-0">{!! nl2br(e($detailLayanan->konten)) !!}</small>
                  </div>
                  @if (isset($detailLayanan->gambar) && $detailLayanan->gambar)
                    <div class="col-12">
                      <hr class="my-2">
                      <span class="text-muted d-block mb-2">Lampiran Gambar</span>
                      <div class="text-center border rounded p-3 bg-light mb-2">
                        <img src="{{ asset('storage/' . $detailLayanan->gambar) }}" alt="Preview"
                          class="img-fluid rounded">
                      </div>
                      <a href="{{ asset('storage/' . $detailLayanan->gambar) }}" target="_blank"
                        class="btn btn-sm btn-primary">
                        <i class='bx bx-image me-1'></i> Lihat Gambar Penuh
                      </a>
                    </div>
                  @endif
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif

    {{-- TOMBOL KEMBALI --}}
    <div class="row">
      <div class="col-12">
        <a href="{{ route('admin_unit.ticket.index') }}" class="btn btn-primary w-100">
          Semua Daftar Tiket Layanan
          <i class="icon-base ti tabler-trending-up ms-2"></i>
        </a>
      </div>
    </div>
  </div>
  @if (session('success'))
    <script>
      window.serviceTicketSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  @if (session('error'))
    <script>
      window.serviceTicketErrorMessage = "{{ session('error') }}";
    </script>
  @endif

  @if ($errors->any())
    <script>
      window.serviceTickeErrorMessage = "{{ $errors->first() }}";
    </script>
  @endif
@endsection
