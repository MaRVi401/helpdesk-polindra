@extends('layouts/contentNavbarLayout')

@section('title', 'Monitoring Tiket')

@section('content')
{{-- CSS sesuai referensi Admin --}}
<style>
    /* Sembunyikan icon pagination bawaan laravel */
    .pagination .page-link svg { display: none !important; }
    .pagination .page-link { font-size: 0.85rem; vertical-align: middle; }

    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; color: white; text-transform: capitalize; }
    
    /* Warna Status */
    .status-diajukan_oleh_pemohon { background-color: #a0aec0; color: #2d3748; } /* Abu-abu */
    .status-ditangani_oleh_pic, .status-diselesaikan_oleh_pic { background-color: #f6ad55; color: white; } /* Kuning */
    .status-dinilai_belum_selesai_oleh_pemohon, .status-pemohon_bermasalah { background-color: #f56565; color: white; } /* Merah */
    .status-dinilai_selesai_oleh_kepala, .status-dinilai_selesai_oleh_pemohon { background-color: #48bb78; color: white; } /* Hijau */

    /* Warna Prioritas */
    .badge { padding: 4px 8px; border-radius: 4px; color: white; font-size: 0.8rem; font-weight: bold; }
    .prioritas-rendah { background-color: #a0aec0; } 
    .prioritas-sedang { background-color: #48bb78; } 
    .prioritas-tinggi { background-color: #f56565; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kepala Unit /</span> Monitoring Tiket
    </h4>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">Daftar Tiket Masuk</h5>
                
                <!-- Toolbar Filter -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form action="{{ route('kepala-unit.monitoring.index') }}" method="GET" class="d-flex gap-2 flex-wrap" id="filterForm">
                        
                        <!-- Filter Status -->
                        <select name="status" class="form-select form-select-sm w-auto text-capitalize" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Dinilai_Selesai_oleh_Kepala" {{ request('status') == 'Dinilai_Selesai_oleh_Kepala' ? 'selected' : '' }}>Dinilai selesai oleh Kepala</option>
                            <option value="Pemohon_Bermasalah" {{ request('status') == 'Pemohon_Bermasalah' ? 'selected' : '' }}>Pemohon Bermasalah</option>
                        </select>

                        <!-- Filter Prioritas -->
                        <select name="prioritas" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            <option value="">Semua Prioritas</option>
                            <option value="3" {{ request('prioritas') == '3' ? 'selected' : '' }}>Tinggi</option>
                            <option value="2" {{ request('prioritas') == '2' ? 'selected' : '' }}>Sedang</option>
                            <option value="1" {{ request('prioritas') == '1' ? 'selected' : '' }}>Rendah</option>
                        </select>

                        <!-- Search -->
                        <div class="input-group input-group-merge">
                            <span class="input-group-text py-1"><i class="bx bx-search"></i></span>
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari No/Judul..." value="{{ request('q') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No Tiket</th>
                        <th>Pemohon</th>
                        <th>Layanan</th>
                        <th>Unit</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tikets as $tiket)
                    <tr>
                        <td><strong>#{{ $tiket->no_tiket }}</strong></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $tiket->pemohon->name ?? 'Guest' }}</span>
                                <small class="text-muted">{{ $tiket->created_at->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($tiket->layanan->nama ?? '-', 25) }}</td>
                        <td>
                            <!-- Unit Pemilik Tiket -->
                            <span class="badge bg-label-secondary">{{ $tiket->layanan->unit->nama_unit ?? '-' }}</span>
                        </td>
                        <td>
                            @php
                                // Fallback Prioritas
                                $prio = $tiket->prioritas ?? $tiket->layanan->prioritas ?? 2;
                                $prioLabel = 'Sedang';
                                $prioClass = 'prioritas-sedang';

                                if ($prio == 3 || $prio === 'Tinggi') {
                                    $prioLabel = 'Tinggi';
                                    $prioClass = 'prioritas-tinggi';
                                } elseif ($prio == 1 || $prio === 'Rendah') {
                                    $prioLabel = 'Rendah';
                                    $prioClass = 'prioritas-rendah';
                                }
                            @endphp
                            <span class="badge {{ $prioClass }}">{{ $prioLabel }}</span>
                        </td>
                        <td>
                            @php
                                $status = $tiket->statusTerbaru->status ?? 'Diajukan_oleh_Pemohon';
                                $statusClass = 'status-' . strtolower($status);
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ str_replace('_', ' ', $status) }}</span>
                        </td>
                        <td>
                            <!-- Tombol Detail (Eye) -->
                            <a href="{{ route('kepala-unit.monitoring.show', $tiket->id) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Lihat Detail">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" alt="No Data" width="100" class="mb-3">
                                <h6 class="mb-0">Tidak ada tiket ditemukan.</h6>
                                <small class="text-muted">Tiket akan muncul jika ada di unit Anda atau ditugaskan ke Anda.</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $tikets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection