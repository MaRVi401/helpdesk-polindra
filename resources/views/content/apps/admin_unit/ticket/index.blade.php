@extends('layouts/contentNavbarLayout')

@section('title', 'Daftar Tiket Masuk')

@section('content')
<style>
    /* --- CSS Warna Status (Konsisten dengan View Show) --- */
    .status-badge { padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: white; text-transform: capitalize; display: inline-block; min-width: 100px; text-align: center;}
    
    /* Mapping Warna */
    .status-diajukan_oleh_pemohon { background-color: #a0aec0; color: #fff; } /* Abu-abu */
    .status-ditangani_oleh_pic { background-color: #f6ad55; color: white; } /* Orange */
    .status-diselesaikan_oleh_pic { background-color: #f6ad55; color: white; border: 1px solid white; } /* Orange */
    .status-pemohon_bermasalah { background-color: #f56565; color: white; } /* Merah */
    .status-ditolak { background-color: #e53e3e; color: white; } /* Merah Tua */
    .status-dinilai_selesai_oleh_kepala { background-color: #48bb78; color: white; } /* Hijau */
    .status-dinilai_selesai_oleh_pemohon { background-color: #38a169; color: white; } /* Hijau Tua */
    
    /* Prioritas Label */
    .priority-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .prio-rendah { background-color: #a0aec0; }
    .prio-sedang { background-color: #ecc94b; }
    .prio-tinggi { background-color: #f56565; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Admin Unit /</span> Tiket Masuk
            </h4>
            <small class="text-muted">Daftar tiket dikelompokkan berdasarkan layanan yang Anda tangani.</small>
        </div>
        <div class="col-md-6 text-end">
             @if(isset($totalTiket))
                <span class="badge bg-primary p-2">Total Tiket: {{ $totalTiket }}</span>
            @endif
        </div>
    </div>

    @if(isset($isPic) && $isPic === false)
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            <div>
                <strong>Akses Terbatas!</strong> Akun Anda belum diatur sebagai PIC untuk Layanan manapun. Silakan hubungi Kepala Unit.
            </div>
        </div>
    @else
        
        {{-- Filter Global --}}
        <div class="card mb-4">
            <div class="card-body p-3">
                <form action="{{ route('admin_unit.ticket.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" name="q" class="form-control" placeholder="Cari No Tiket / Nama Pemohon..." value="{{ request('q') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">- Semua Status -</option>
                                <option value="Diajukan_oleh_Pemohon" {{ request('status') == 'Diajukan_oleh_Pemohon' ? 'selected' : '' }}>Baru Masuk</option>
                                <option value="Ditangani_oleh_PIC" {{ request('status') == 'Ditangani_oleh_PIC' ? 'selected' : '' }}>Sedang Ditangani</option>
                                <option value="Diselesaikan_oleh_PIC" {{ request('status') == 'Diselesaikan_oleh_PIC' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="Dinilai_Selesai_oleh_Kepala" {{ request('status') == 'Dinilai_Selesai_oleh_Kepala' ? 'selected' : '' }}>Selesai (Final)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                             <a href="{{ route('admin_unit.ticket.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- LOOPING PER LAYANAN (TABEL TERPISAH) --}}
        @forelse($data_layanan as $layanan)
            <div class="card mb-5 border border-primary">
                <div class="card-header bg-label-primary d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-primary fw-bold"><i class='bx bx-layer me-2'></i>{{ $layanan->nama }}</h5>
                    <span class="badge bg-white text-primary">{{ $layanan->tiket->count() }} Tiket</span>
                </div>
                
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">No Tiket</th> {{-- Lebar disesuaikan --}}
                                <th width="25%">Pemohon</th>  {{-- Lebar disesuaikan --}}
                                <th width="15%">Prioritas</th>
                                <th width="20%">Status</th>    {{-- Lebar disesuaikan --}}
                                <th width="15%">Tgl Masuk</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($layanan->tiket as $tiket)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary">#{{ $tiket->no_tiket }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $tiket->pemohon->name }}</span>
                                        <small class="text-muted" style="font-size: 11px;">
                                            {{ $tiket->pemohon->email }}
                                        </small>
                                    </div>
                                </td>
                                
                                {{-- KOLOM PRIORITAS --}}
                                <td>
                                    @php
                                        $prio = $layanan->prioritas ?? 1;
                                        $prioText = match($prio) { 3 => 'Tinggi', 2 => 'Sedang', default => 'Rendah' };
                                        $prioClass = match($prio) { 3 => 'prio-tinggi', 2 => 'prio-sedang', default => 'prio-rendah' };
                                        $badgePrio = match($prio) { 3 => 'danger', 2 => 'warning', default => 'secondary' };
                                    @endphp
                                    <span class="badge bg-label-{{ $badgePrio }}">
                                        <span class="priority-dot {{ $prioClass }}"></span> {{ $prioText }}
                                    </span>
                                </td>

                                {{-- KOLOM STATUS --}}
                                <td>
                                    @php
                                        $st = $tiket->statusTerbaru?->status ?? 'Baru';
                                        $cssClass = 'status-' . strtolower($st);
                                    @endphp
                                    <span class="status-badge {{ $cssClass }}">
                                        {{ str_replace('_', ' ', $st) }}
                                    </span>
                                </td>

                                <td>
                                    <span title="{{ $tiket->created_at }}">
                                        {{ $tiket->created_at->format('d M Y') }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $tiket->created_at->format('H:i') }} WIB</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin_unit.ticket.show', $tiket->id) }}" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="bx bx-search-alt me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class='bx bx-envelope-open fs-3 mb-2'></i><br>
                                    Tidak ada tiket ditemukan untuk layanan ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center p-5">
                <i class='bx bx-data fs-1 mb-3'></i>
                <h5>Belum ada data Layanan.</h5>
                <p>Anda belum ditugaskan sebagai PIC pada layanan manapun, atau data layanan kosong.</p>
            </div>
        @endforelse
    @endif
</div>
@endsection