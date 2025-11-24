@extends('layouts/contentNavbarLayout')

@section('title', 'Monitoring Tiket')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kepala Unit /</span> Monitoring Tiket
    </h4>

    <!-- Info Banner -->
    <div class="alert alert-primary d-flex align-items-center" role="alert">
        <i class="bx bx-info-circle me-2"></i>
        <div>
            Menampilkan tiket dari Layanan di mana Anda ditugaskan sebagai <strong>PIC (Penanggung Jawab)</strong>.
        </div>
    </div>

    <!-- Statistik Singkat -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-envelope"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $tikets->total() }}</h4>
                    </div>
                    <p class="mb-1">Total Tiket Ditangani</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">Daftar Tiket Masuk</h5>
            
            <!-- Filter Section -->
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role">
                    <form action="{{ route('kepala-unit.monitoring.index') }}" method="GET" id="formStatus">
                        <!-- Filter Status sesuai AdminTiketController -->
                        <select name="status" class="form-select text-capitalize" onchange="document.getElementById('formStatus').submit()">
                            <option value=""> Pilih Status </option>
                            <option value="Diajukan_oleh_Pemohon" {{ request('status') == 'Diajukan_oleh_Pemohon' ? 'selected' : '' }}>Diajukan oleh Pemohon</option>
                            <option value="Dinilai_Selesai_oleh_Kepala" {{ request('status') == 'Dinilai_Selesai_oleh_Kepala' ? 'selected' : '' }}>Dinilai Selesai oleh Kepala</option>
                            <option value="Pemohon_Bermasalah" {{ request('status') == 'Pemohon_Bermasalah' ? 'selected' : '' }}>Pemohon Bermasalah</option>
                        </select>
                        
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        @if(request('prioritas')) <input type="hidden" name="prioritas" value="{{ request('prioritas') }}"> @endif
                    </form>
                </div>

                <!-- Filter Prioritas -->
                <div class="col-md-4 user_plan">
                    <form action="{{ route('kepala-unit.monitoring.index') }}" method="GET" id="formPrioritas">
                        <select name="prioritas" class="form-select text-capitalize" onchange="document.getElementById('formPrioritas').submit()">
                            <option value="">Semua Prioritas</option>
                            <option value="1" {{ request('prioritas') == '1' ? 'selected' : '' }}>Tinggi</option>
                            <option value="2" {{ request('prioritas') == '2' ? 'selected' : '' }}>Sedang</option>
                            <option value="3" {{ request('prioritas') == '3' ? 'selected' : '' }}>Rendah</option>
                        </select>
                        
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    </form>
                </div>

                <div class="col-md-4 d-flex justify-content-end">
                     <form action="{{ route('kepala-unit.monitoring.index') }}" method="GET" class="w-100">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="q" class="form-control" placeholder="Cari ID / Judul..." value="{{ request('q') }}">
                        </div>
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                        @if(request('prioritas')) <input type="hidden" name="prioritas" value="{{ request('prioritas') }}"> @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Tiket</th>
                        <th>Pemohon</th>
                        <th>Layanan</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($tikets as $tiket)
                    <tr>
                        <td>
                            <span class="fw-bold">#{{ $tiket->no_tiket }}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-start align-items-center user-name">
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($tiket->pemohon->name ?? 'U', 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-body text-truncate">
                                        <span class="fw-semibold">{{ $tiket->pemohon->name ?? 'Guest' }}</span>
                                    </a>
                                    <small class="text-muted">{{ $tiket->pemohon->email ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-truncate d-flex align-items-center">
                                <span class="badge badge-center rounded-pill bg-label-warning w-px-30 h-px-30 me-2">
                                    <i class="bx bx-laptop bx-xs"></i>
                                </span>
                                {{ \Illuminate\Support\Str::limit($tiket->layanan->nama ?? '-', 25) }}
                            </span>
                        </td>
                        <td>
                            @php
                                // Mapping Prioritas Sesuai Referensi HTML Anda
                                // 1 = Tinggi (Merah/Danger)
                                // 2 = Sedang (Hijau/Success - Sesuai referensi css .prioritas-2)
                                // 3 = Rendah (Abu/Secondary - Sesuai referensi css .prioritas-1 tapi di DB biasanya 3 itu rendah)
                                
                                $prio = $tiket->prioritas;
                                $prioLabel = 'Rendah';
                                $prioClass = 'bg-label-secondary';

                                if ($prio == 1) {
                                    $prioLabel = 'Tinggi';
                                    $prioClass = 'bg-label-danger'; // Merah
                                } elseif ($prio == 2) {
                                    $prioLabel = 'Sedang';
                                    $prioClass = 'bg-label-success'; // Hijau (Sesuai Referensi Anda)
                                } elseif ($prio == 3) {
                                    $prioLabel = 'Rendah';
                                    $prioClass = 'bg-label-secondary'; // Abu-abu
                                }
                            @endphp
                            <span class="badge {{ $prioClass }}">{{ $prioLabel }}</span>
                        </td>
                        <td>
                            @php
                                $status = $tiket->statusTerbaru->status ?? '-';
                                $badgeClass = 'bg-label-secondary';

                                switch ($status) {
                                    case 'Diajukan_oleh_Pemohon':
                                        $badgeClass = 'bg-label-primary'; // Biru
                                        break;
                                    case 'Ditangani_oleh_PIC':
                                        $badgeClass = 'bg-label-warning'; // Kuning
                                        break;
                                    case 'Diselesaikan_oleh_PIC':
                                    case 'Dinilai_Selesai_oleh_Kepala':
                                    case 'Dinilai_Selesai_oleh_Pemohon':
                                        $badgeClass = 'bg-label-success'; // Hijau
                                        break;
                                    case 'Dinilai_Belum_Selesai_oleh_Pemohon':
                                    case 'Pemohon_Bermasalah':
                                        $badgeClass = 'bg-label-danger'; // Merah
                                        break;
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $status) }}</span>
                        </td>
                        <td>
                            {{ $tiket->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('kepala-unit.monitoring.show', $tiket->id) }}" class="text-body" data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="bx bx-show mx-1"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                             <div class="d-flex flex-column align-items-center">
                                <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" alt="No Data" width="100" class="mb-3">
                                <h6 class="mb-0">Belum ada tiket yang ditugaskan ke Anda.</h6>
                                <small class="text-muted">Tiket akan muncul di sini jika Anda ditunjuk sebagai PIC layanan tersebut.</small>
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