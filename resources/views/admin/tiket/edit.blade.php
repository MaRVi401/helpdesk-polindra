@use('Illuminate\Support\Str')
<!DOCTYPE html>
<html>

<head>
    <title>Detail Tiket: #{{ $tiket->no_tiket }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
            line-height: 1.5;
            padding: 20px;
        }

        .main-container {
            background-color: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .header h1 {
            margin: 0;
        }

        .header-info {
            text-align: right;
        }

        .button {
            display: inline-block;
            padding: 10px 16px;
            border: 1px solid transparent;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .button-primary {
            background-color: #4299e1;
            color: white;
            border-color: #4299e1;
        }

        .button-secondary {
            background-color: #e2e8f0;
            color: #2d3748;
            border-color: #cbd5e0;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }

        .alert-success {
            color: #2f855a;
            background-color: #c6f6d5;
        }

        .alert-error {
            color: #9b2c2c;
            background-color: #fed7d7;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        .ticket-layout {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .ticket-main {
            flex: 2;
            min-width: 400px;
        }

        .ticket-sidebar {
            flex: 1;
            min-width: 300px;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1rem 1.5rem;
            background-color: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-body p:first-child {
            margin-top: 0;
        }

        .card-body p:last-child {
            margin-bottom: 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1rem;
        }

        .info-grid dt {
            font-weight: 600;
            color: #4a5568;
        }

        .info-grid dd {
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a5568;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .timeline {
            border-left: 3px solid #e2e8f0;
            margin-left: 10px;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        /* Warna default dot untuk riwayat status */
        .timeline-dot {
            position: absolute;
            left: -31px;
            top: 5px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #4299e1;
        }

        .timeline-time {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 0.25rem;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .timeline-body {
            font-size: 0.95rem;
        }

        .timeline-body p {
            margin: 0;
        }

        .timeline-body-comment {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            border-radius: 6px;
        }

        /* --- CSS Status Badge Berdasarkan Nilai ENUM Baru (untuk header) --- */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            text-transform: capitalize;
        }

        /* Abu-abu: Diajukan oleh Pemohon */
        .status-diajukan_oleh_pemohon {
            background-color: #a0aec0;
            /* Abu-abu */
        }

        /* Kuning: Ditangani/Diselesaikan oleh PIC */
        .status-ditangani_oleh_pic,
        .status-diselesaikan_oleh_pic {
            background-color: #f6ad55;
            /* Kuning */
        }

        /* Merah: Belum Selesai/Bermasalah */
        .status-dinilai_belum_selesai_oleh_pemohon,
        .status-pemohon_bermasalah {
            background-color: #f56565;
            /* Merah */
        }

        /* Hijau: Selesai oleh Kepala/Pemohon */
        .status-dinilai_selesai_oleh_kepala,
        .status-dinilai_selesai_oleh_pemohon {
            background-color: #48bb78;
            /* Hijau */
        }


        /* --- CSS Khusus Role Komentar --- */
        /* Warna untuk super_admin (misalnya: Merah) */
        .dot-super_admin {
            background-color: #e53e3e !important;
        }

        .body-super_admin {
            background-color: #fef2f2 !important;
            border-color: #fbd7d7 !important;
        }

        /* Warna untuk mahasiswa (misalnya: Biru Muda) */
        .dot-mahasiswa {
            background-color: #4299e1 !important;
        }

        .body-mahasiswa {
            background-color: #ebf8ff !important;
            border-color: #bee3f8 !important;
        }

        /* Warna untuk kepala_unit (misalnya: Ungu) */
        .dot-kepala_unit {
            background-color: #805ad5 !important;
        }

        .body-kepala_unit {
            background-color: #faf5ff !important;
            border-color: #e9d8fd !important;
        }

        /* Warna untuk admin_unit (misalnya: Hijau) */
        .dot-admin_unit {
            background-color: #38a169 !important;
        }

        .body-admin_unit {
            background-color: #f0fff4 !important;
            border-color: #c6f6d5 !important;
        }

        /* Tambahan style untuk tag status di riwayat (Sesuai dengan kode Anda di bawah) */
        .bg-gray-200 {
            background-color: #edf2f7;
            color: #2d3748;
        }

        .bg-orange-100 {
            background-color: #feebc8;
            color: #9c4221;
        }

        /* Kuning muda/Orange muda */
        .bg-red-100 {
            background-color: #fed7d7;
            color: #9b2c2c;
        }

        /* Merah muda */
        .bg-green-100 {
            background-color: #c6f6d5;
            color: #2f855a;
        }

        /* Hijau muda */
        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .inline-flex {
            display: inline-flex;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .leading-5 {
            line-height: 1.25rem;
        }

        .font-semibold {
            font-weight: 600;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        /* --- CSS Tambahan untuk Coloring Opsi Select --- */

        .option-diajukan_oleh_pemohon {
            background-color: #edf2f7 !important;
            color: #2d3748 !important;
        }

        /* Abu-abu */
        .option-ditangani_oleh_pic {
            background-color: #fef2c5 !important;
            color: #f6ad55 !important;
        }

        /* Kuning Muda */
        .option-diselesaikan_oleh_pic {
            background-color: #fef2c5 !important;
            color: #f6ad55 !important;
        }

        /* Kuning Muda */
        .option-dinilai_belum_selesai_oleh_pemohon {
            background-color: #fed7d7 !important;
            color: #9b2c2c !important;
        }

        /* Merah Muda */
        .option-pemohon_bermasalah {
            background-color: #fed7d7 !important;
            color: #9b2c2c !important;
        }

        /* Merah Muda */
        .option-dinilai_selesai_oleh_kepala {
            background-color: #c6f6d5 !important;
            color: #2f855a !important;
        }

        /* Hijau Muda */
        .option-dinilai_selesai_oleh_pemohon {
            background-color: #c6f6d5 !important;
            color: #2f855a !important;
        }

        /* Hijau Muda */

        /* Atur warna text/border untuk Select Box saat ada opsi terpilih agar terlihat */
        #status.status-diajukan_oleh_pemohon {
            background-color: #edf2f7;
            color: #2d3748;
            border-color: #a0aec0;
        }

        #status.status-ditangani_oleh_pic {
            background-color: #fef2c5;
            color: #f6ad55;
            border-color: #f6ad55;
        }

        #status.status-diselesaikan_oleh_pic {
            background-color: #fef2c5;
            color: #f6ad55;
            border-color: #f6ad55;
        }

        #status.status-dinilai_belum_selesai_oleh_pemohon {
            background-color: #fed7d7;
            color: #9b2c2c;
            border-color: #f56565;
        }

        #status.status-pemohon_bermasalah {
            background-color: #fed7d7;
            color: #9b2c2c;
            border-color: #f56565;
        }

        #status.status-dinilai_selesai_oleh_kepala {
            background-color: #c6f6d5;
            color: #2f855a;
            border-color: #48bb78;
        }

        #status.status-dinilai_selesai_oleh_pemohon {
            background-color: #c6f6d5;
            color: #2f855a;
            border-color: #48bb78;
        }
    </style>
</head>

<body>
    <div class="main-container">

        <div class="header">
            <div>
                <h1>Tiket #{{ $tiket->no_tiket }}</h1>
                <p style="margin:0; color: #718096;">Dibuat pada: {{ $tiket->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="header-info">
                @php
                    // Ambil status ENUM dan konversi untuk tampilan
                    $status = $statusSekarang;
                    $statusClass = 'status-' . strtolower($status);
                    $statusHeaderLabel = str_replace('_', ' ', $status); // Tampilan rapi di header
                @endphp
                <span class="status-badge {{ $statusClass }}" style="font-size: 1rem;">{{ $statusHeaderLabel }}</span>
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

                <div class="card">
                    <div class="card-header">Balas Tiket / Ubah Status</div>
                    <div class="card-body">
                        <form action="{{ route('admin.tiket.update', $tiket) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="komentar">Tambah Balasan / Komentar</label>
                                <textarea name="komentar" id="komentar" placeholder="Tulis balasan untuk pemohon atau catatan internal..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="status">Ubah Status Tiket</label>
                                {{-- Tambahkan kelas status saat ini pada elemen SELECT --}}
                                <select name="status" id="status" class="status-{{ strtolower($statusSekarang) }}">
                                    @foreach ($statuses as $status)
                                        @php
                                            $optionClass = 'option-' . strtolower($status);
                                            $optionLabel = str_replace('_', ' ', $status);
                                        @endphp
                                        <option value="{{ $status }}"
                                            {{ $statusSekarang == $status ? 'selected' : '' }}
                                            class="{{ $optionClass }}">
                                            {{ $optionLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="button button-primary">Perbarui Tiket</button>
                            <a href="{{ route('admin.tiket.index') }}" class="button button-secondary"
                                style="float: right;">Kembali ke Daftar</a>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Riwayat Komentar ({{ $tiket->komentar->count() }})</div>
                    <div class="card-body">
                        <div class="timeline">
                            @forelse($tiket->komentar as $komen)
                                @php
                                    // 1. Ambil role pengirim
                                    $role = $komen->pengirim->role ?? 'unknown';

                                    // 2. Tentukan kelas CSS berdasarkan role
                                    $dotClass = 'dot-' . $role;
                                    $bodyClass = 'body-' . $role;
                                @endphp
                                <div class="timeline-item">
                                    {{-- Gunakan kelas dotClass pada span timeline-dot --}}
                                    <span class="timeline-dot {{ $dotClass }}"></span>
                                    <div class="timeline-time">{{ $komen->created_at->format('d M Y, H:i') }}</div>
                                    <div class="timeline-title">
                                        {{-- Tampilkan nama dan role --}}
                                        {{ $komen->pengirim->name }} (<strong
                                            style="text-transform: capitalize;">{{ $role }}</strong>)
                                    </div>
                                    {{-- Gunakan kelas bodyClass pada div timeline-body-comment --}}
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
                            <dt>Nama</dt>
                            <dd>{{ $tiket->pemohon->name ?? 'N/A' }}</dd>

                            <dt>Email</dt>
                            <dd>{{ $tiket->pemohon->email ?? 'N/A' }}</dd>

                            <dt>NIM</dt>
                            <dd>{{ $tiket->pemohon->mahasiswa->nim ?? 'N/A' }}</dd>

                            <dt>Jurusan</dt>
                            <dd>{{ $tiket->pemohon->mahasiswa->programStudi->jurusan->nama_jurusan ?? 'N/A' }}</dd>

                            <dt>Program Studi</dt>
                            <dd>{{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Informasi Tiket</div>
                    <div class="card-body">
                        <dl class="info-grid">
                            <dt>Layanan</dt>
                            <dd>{{ $tiket->layanan->nama ?? 'N/A' }}</dd>

                            <dt>Unit</dt>
                            <dd>{{ $tiket->layanan->unit->nama_unit ?? 'N/A' }}</dd>

                            <dt>Deskripsi Awal</dt>
                            <dd>{!! nl2br(e($tiket->deskripsi)) !!}</dd>
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

                                    @if ($detailLayanan->gambar)
                                        <dt>Lampiran Gambar</dt>
                                        <dd>
                                            <a href="{{ asset('storage/' . $detailLayanan->gambar) }}"
                                                target="_blank" class="text-blue-500 underline">
                                                Lihat Gambar
                                            </a>
                                        </dd>
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
                            {{-- Urutkan berdasarkan waktu dibuat terbaru muncul di atas --}}
                            @foreach ($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                                @php
                                    $statusValue = $riwayat->status;
                                    $role = $riwayat->user->role ?? 'Sistem';
                                    $labelClass = '';
                                    $dotColor = '';

                                    switch ($statusValue) {
                                        case 'Diajukan_oleh_Pemohon':
                                            $statusLabel = 'Diajukan oleh Pemohon';
                                            $labelClass = 'bg-gray-200 text-gray-800'; // Abu-abu
                                            $dotColor = '#a0aec0';
                                            break;
                                        case 'Ditangani_oleh_PIC':
                                            $statusLabel = 'Ditangani oleh PIC';
                                            $labelClass = 'bg-orange-100 text-orange-800'; // Kuning
                                            $dotColor = '#f6ad55';
                                            break;
                                        case 'Diselesaikan_oleh_PIC':
                                            $statusLabel = 'Diselesaikan oleh PIC';
                                            $labelClass = 'bg-orange-100 text-orange-800'; // Kuning
                                            $dotColor = '#f6ad55';
                                            break;
                                        case 'Dinilai_Belum_Selesai_oleh_Pemohon':
                                            $statusLabel = 'Dinilai Belum Selesai oleh Pemohon';
                                            $labelClass = 'bg-red-100 text-red-800'; // Merah
                                            $dotColor = '#f56565';
                                            break;
                                        case 'Pemohon_Bermasalah':
                                            $statusLabel = 'Pemohon Bermasalah';
                                            $labelClass = 'bg-red-100 text-red-800'; // Merah
                                            $dotColor = '#f56565';
                                            break;
                                        case 'Dinilai_Selesai_oleh_Kepala':
                                            $statusLabel = 'Dinilai Selesai oleh Kepala';
                                            $labelClass = 'bg-green-100 text-green-800'; // Hijau
                                            $dotColor = '#48bb78';
                                            break;
                                        case 'Dinilai_Selesai_oleh_Pemohon':
                                            $statusLabel = 'Dinilai Selesai oleh Pemohon';
                                            $labelClass = 'bg-green-100 text-green-800'; // Hijau
                                            $dotColor = '#48bb78';
                                            break;
                                        default:
                                            $statusLabel = 'Status Tidak Dikenal';
                                            $labelClass = 'bg-white text-black';
                                            $dotColor = '#000000';
                                            break;
                                    }
                                @endphp
                                <div class="timeline-item">
                                    {{-- Terapkan warna dot yang sudah ditentukan (Sesuai dengan warna di gambar) --}}
                                    <span class="timeline-dot" style="background-color: {{ $dotColor }};"></span>
                                    <div class="timeline-time">{{ $riwayat->created_at->format('d M Y, H:i') }}</div>

                                    {{-- Gunakan $statusLabel dan $labelClass untuk tampilan tag status --}}
                                    <div class="timeline-title">
                                        {{-- Style inline untuk tag status di riwayat (Sesuai dengan warna di gambar) --}}
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $labelClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <div class="timeline-body">
                                        <p>Oleh: {{ $riwayat->user->name ?? 'Sistem' }} (<strong
                                                style="text-transform: capitalize;">{{ $role }}</strong>)</p>
                                    </div>
                                </div>
                            @endforeach
                            @if ($tiket->riwayatStatus->isEmpty())
                                <p>Belum ada riwayat status tercatat.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</body>

</html>
