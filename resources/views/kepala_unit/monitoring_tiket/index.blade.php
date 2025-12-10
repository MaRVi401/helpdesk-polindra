@extends('layouts/contentNavbarLayout')

@section('title', 'Monitoring Tiket')

@section('content')
    <style>
        .pagination .page-link svg {
            display: none !important;
        }

        .pagination .page-link {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            text-transform: capitalize;
        }

        .status-diajukan_oleh_pemohon {
            background-color: #a0aec0;
            color: #2d3748;
        }

        .status-ditangani_oleh_pic,
        .status-diselesaikan_oleh_pic {
            background-color: #f6ad55;
            color: white;
        }

        .status-dinilai_belum_selesai_oleh_pemohon,
        .status-pemohon_bermasalah {
            background-color: #f56565;
            color: white;
        }

        .status-dinilai_selesai_oleh_kepala,
        .status-dinilai_selesai_oleh_pemohon {
            background-color: #48bb78;
            color: white;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .prioritas-rendah {
            background-color: #a0aec0;
        }

        .prioritas-sedang {
            background-color: #48bb78;
        }

        .prioritas-tinggi {
            background-color: #f56565;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Kepala Unit /</span> Monitoring Tiket
        </h4>

        {{-- LOGIKA PESAN PERINGATAN JIKA TIDAK PUNYA UNIT --}}
        @if (isset($unitsDipimpin) && $unitsDipimpin->isEmpty())
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bx bx-error-circle me-2"></i>
                <div>
                    Kepala belum di set ke PIC Unit
                </div>
            </div>
        @else
            {{-- 1. CARD FILTER --}}
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0"><i class='bx bx-filter-alt'></i> Filter Tiket Masuk</h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <form action="{{ route('kepala-unit.monitoring.index') }}" method="GET"
                                class="d-flex gap-2 flex-wrap" id="filterForm">

                                {{-- FILTER UNIT (Hanya Muncul Jika Lebih dari 1 Unit) --}}
                                @if (isset($unitsDipimpin) && $unitsDipimpin->count() > 1)
                                    <select name="unit_id" class="form-select form-select-sm w-auto"
                                        onchange="this.form.submit()">
                                        <option value="">Semua Unit Saya</option>
                                        @foreach ($unitsDipimpin as $u)
                                            <option value="{{ $u->id }}"
                                                {{ request('unit_id') == $u->id ? 'selected' : '' }}>
                                                {{ $u->nama_unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif

                                <select name="status" class="form-select form-select-sm w-auto text-capitalize"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="Dinilai_Selesai_oleh_Kepala"
                                        {{ request('status') == 'Dinilai_Selesai_oleh_Kepala' ? 'selected' : '' }}>Dinilai
                                        selesai oleh Kepala</option>
                                    <option value="Pemohon_Bermasalah"
                                        {{ request('status') == 'Pemohon_Bermasalah' ? 'selected' : '' }}>Pemohon Bermasalah
                                    </option>
                                </select>

                                <select name="prioritas" class="form-select form-select-sm w-auto"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Prioritas</option>
                                    <option value="3" {{ request('prioritas') == '3' ? 'selected' : '' }}>Tinggi
                                    </option>
                                    <option value="2" {{ request('prioritas') == '2' ? 'selected' : '' }}>Sedang
                                    </option>
                                    <option value="1" {{ request('prioritas') == '1' ? 'selected' : '' }}>Rendah
                                    </option>
                                </select>

                                <div class="input-group input-group-merge w-auto">
                                    <span class="input-group-text py-1"><i class="bx bx-search"></i></span>
                                    <input type="text" name="q" class="form-control form-control-sm"
                                        placeholder="Cari No/Judul..." value="{{ request('q') }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LOGIKA GROUPING: Kelompokkan tiket berdasarkan Nama Unit --}}
            @php
                $groupedTikets = $tikets->groupBy(function ($item) {
                    return $item->layanan->unit->nama_unit ?? 'Lainnya';
                });
            @endphp

            {{-- 2. LOOPING CARD TABEL BERDASARKAN UNIT --}}
            @forelse($groupedTikets as $unitName => $unitTikets)
                <div class="card mb-4">
                    {{-- HEADER DENGAN JUMLAH TIKET --}}
                    <div class="card-header border-bottom bg-lighter d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary"><i class='bx bx-building me-2'></i> Unit: {{ $unitName }}</h5>
                        <span class="badge bg-label-primary">{{ $unitTikets->count() }} Tiket</span>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No Tiket</th>
                                    <th>Pemohon</th>
                                    <th>Layanan</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unitTikets as $tiket)
                                    <tr>
                                        <td><strong>{{ $tiket->no_tiket }}</strong></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">{{ $tiket->pemohon->name ?? 'Guest' }}</span>
                                                <small class="text-muted">{{ $tiket->created_at->diffForHumans() }}</small>
                                            </div>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($tiket->layanan->nama ?? '-', 30) }}</td>
                                        <td>
                                            @php
                                                $prio = $tiket->prioritas ?? ($tiket->layanan->prioritas ?? 2);
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
                                            <span
                                                class="status-badge {{ $statusClass }}">{{ str_replace('_', ' ', $status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('kepala-unit.monitoring.show', $tiket->id) }}"
                                                class="btn btn-sm btn-primary shadow-sm" title="Lihat Detail">
                                                <i class="bx bx-search-alt me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                {{-- JIKA TIDAK ADA TIKET SAMA SEKALI (TAPI PUNYA UNIT) --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" alt="No Data"
                            width="100" class="mb-3">
                        <h6 class="mb-0">Tidak ada tiket ditemukan.</h6>
                        <small class="text-muted">Tiket akan muncul jika ada di unit Anda atau ditugaskan ke Anda.</small>
                    </div>
                </div>
            @endforelse

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $tikets->links() }}
            </div>
        @endif
    </div>
@endsection
