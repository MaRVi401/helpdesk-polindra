@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Admin Unit')

@section('content')
    <style>
        /* CSS Wajib: Salin semua definisi status badge yang lengkap dari view Monitoring/Kepala Unit */
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
            <span class="text-muted fw-light">Admin Unit /</span> Dashboard
        </h4>

        @if (!isset($unit))
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="ti ti-error-circle me-2"></i>
                <div>
                    Akun Anda belum terasosiasi dengan Unit manapun. Harap hubungi Administrator.
                </div>
            </div>
        @else
            {{-- 1. CARD SAMBUTAN & RINGKASAN --}}
            <div class="row">
                <div class="col-lg-12 mb-4 order-0">
                    <div class="card">
                        <div class="d-flex align-items-end row">
                            <div class="col-sm-7">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">Selamat Datang,
                                        {{ Auth::user()->name ?? 'Admin Unit' }}! 🎉</h5>
                                    <p class="mb-4">
                                        Anda bertanggung jawab untuk mengelola layanan dibawah ini:
                                    </p>

                                    {{-- Tombol Aksi Cepat --}}
                                    <a href="{{ route('admin_unit.ticket.index') }}"
                                        class="btn btn-sm btn-primary shadow-sm me-2">
                                        <i class="ti ti-ticket me-1"></i> Kelola Tiket Layanan
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-5 text-center text-sm-left">
                                <div class="card-body pb-0 px-0 px-md-4">
                                    <img src="{{ asset('https://spada.kemdiktisaintek.go.id/images/adpk2.png') }}"
                                        height="140" alt="View Badge User" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. STATISTIK UTAMA TIKET --}}
            <div class="row">

                {{-- Card A: Total Tiket Unit --}}
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="card-title d-flex align-items-start flex-column">
                                    <span class="fw-semibold d-block mb-1">Total Tiket Unit</span>
                                    <h3 class="card-title mb-0">{{ number_format($totalTiket ?? 0) }}</h3>
                                </div>
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-info"><i
                                            class="ti ti-stack ti-sm"></i></span>
                                </div>
                            </div>
                            <small class="text-muted">Semua tiket Unit Anda.</small>
                        </div>
                    </div>
                </div>

                {{-- Card B: Tiket Baru Masuk --}}
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="card-title d-flex align-items-start flex-column">
                                    <span class="fw-semibold d-block mb-1">Tiket Baru</span>
                                    <h3 class="card-title mb-0">{{ number_format($tiketBaru ?? 0) }}</h3>
                                </div>
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-danger"><i
                                            class="ti ti-bell-ringing ti-sm"></i></span>
                                </div>
                            </div>
                            @php
                                $persenBaru = $totalTiket > 0 ? round(($tiketBaru / $totalTiket) * 100) : 0;
                            @endphp
                            <small class="text-muted text-danger">↑ {{ $persenBaru }}% Menunggu PIC</small>
                        </div>
                    </div>
                </div>

                {{-- Card C: Sedang Diproses --}}
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="card-title d-flex align-items-start flex-column">
                                    <span class="fw-semibold d-block mb-1">Sedang Diproses</span>
                                    <h3 class="card-title mb-0">{{ number_format($tiketDiproses ?? 0) }}</h3>
                                </div>
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-warning"><i
                                            class="ti ti-clock-hour-4 ti-sm"></i></span>
                                </div>
                            </div>
                            @php
                                $persenDiproses = $totalTiket > 0 ? round(($tiketDiproses / $totalTiket) * 100) : 0;
                            @endphp
                            <small class="text-muted text-warning">↑ {{ $persenDiproses }}% dari total.</small>
                        </div>
                    </div>
                </div>

                {{-- Card D: Tiket Selesai --}}
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="card-title d-flex align-items-start flex-column">
                                    <span class="fw-semibold d-block mb-1">Telah Selesai</span>
                                    <h3 class="card-title mb-0">{{ number_format($tiketSelesai ?? 0) }}</h3>
                                </div>
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-success"><i
                                            class="ti ti-checks ti-sm"></i></span>
                                </div>
                            </div>
                            @php
                                $persenSelesai = $totalTiket > 0 ? round(($tiketSelesai / $totalTiket) * 100) : 0;
                            @endphp
                            <small class="text-muted text-success">↑ {{ $persenSelesai }}% Tingkat Selesai</small>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 3. TABEL TIKET TERBARU --}}
            <div class="card mb-4">
                <div class="card-header border-bottom bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class='ti ti-list-details me-2'></i> 5 Tiket Terbaru !</h5>
                    <a href="{{ route('admin_unit.ticket.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Tiket
                    </a>
                </div>

                @if (!empty($latestTikets) && count($latestTikets) > 0)
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
                                @foreach ($latestTikets as $tiket)
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

                                                if ($prio == 3 || strtolower($prio) === 'tinggi') {
                                                    $prioLabel = 'Tinggi';
                                                    $prioClass = 'prioritas-tinggi';
                                                } elseif ($prio == 1 || strtolower($prio) === 'rendah') {
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
                                            <a href="{{ route('admin_unit.ticket.show', $tiket->id) }}"
                                                class="btn btn-sm btn-icon btn-outline-info" title="Lihat Detail">
                                                <i class="ti ti-search"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-center py-4">
                        <h6 class="mb-0">Tidak ada tiket terbaru di Unit ini.</h6>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
