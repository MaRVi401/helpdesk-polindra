@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss', 'resources/assets/vendor/libs/swiper/swiper.scss', 'resources/assets/vendor/fonts/flag-icons.scss'])
@endsection
@section('page-style')
  @vite('resources/assets/vendor/scss/pages/cards-advance.scss')
@endsection
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/swiper/swiper.js'])
@endsection

@section('page-script')
  <script>
    // Pass data dari Laravel ke JavaScript
    window.dashboardData = {
      weeklyData: @json($weekly_data),
      persentaseSelesai: {{ $persentase_selesai }},
      persentaseBelumSelesai: {{ $persentase_belum_selesai }}
    };
  </script>
  @vite('resources/assets/js/management/dashboard/mahasiswa.js')
@endsection

@section('content')
  <div class="row g-6">
    {{-- SLIDE FITUR LAYANAN --}}
    <div class="col-xl-6 col">
      <div class="swiper-container swiper-container-horizontal swiper swiper-card-advance-bg"
        id="swiper-with-pagination-cards">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <div class="row">
              <div class="col-12">
                <h5 class="text-white mb-0">Fitur Layanan</h5>
                <small>Intuitif & Mudah Digunakan</small>
              </div>
              <div class="row">
                <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1 pt-md-9">
                  <h6 class="text-white mt-0 mt-md-3 mb-4">Kelebihan Sistem</h6>
                  <div class="row">
                    <div class="col-6">
                      <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-4 align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg"><i
                              class="icon-base ti tabler-check text-success"></i></p>
                          <p class="mb-0">Intuitif</p>
                        </li>
                        <li class="d-flex align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg"><i
                              class="icon-base ti tabler-check text-success"></i></p>
                          <p class="mb-0">Real-time</p>
                        </li>
                      </ul>
                    </div>
                    <div class="col-6">
                      <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-4 align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg"><i
                              class="icon-base ti tabler-check text-success"></i></p>
                          <p class="mb-0">Responsif</p>
                        </li>
                        <li class="d-flex align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg"><i
                              class="icon-base ti tabler-check text-success"></i></p>
                          <p class="mb-0">Notifikasi</p>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                  <img src="{{ asset('assets/img/illustrations/service-feature.png') }}" alt="Statistik Tiket"
                    height="150" class="card-website-analytics-img" />
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-slide">
            <div class="row">
              <div class="col-12">
                <h5 class="text-white mb-0">Fitur Layanan</h5>
                <small>Fleksibel & Efisien</small>
              </div>
              <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1 pt-md-9">
                <h6 class="text-white mt-0 mt-md-3 mb-4">Manfaat Penggunaan</h6>
                <div class="row">
                  <div class="col-6">
                    <ul class="list-unstyled mb-0">
                      <li class="d-flex mb-4 align-items-center">
                        <p class="mb-0 fw-medium me-2 website-analytics-text-bg"><i
                            class="icon-base ti tabler-check text-success"></i></p>
                        <p class="mb-0">24/7 Support</p>
                      </li>
                      <li class="d-flex align-items-center">
                        <p class="mb-0 fw-medium me-2 website-analytics-text-bg"><i
                            class="icon-base ti tabler-check text-success"></i></p>
                        <p class="mb-0">Monitoring</p>
                      </li>
                    </ul>
                  </div>
                  <div class="col-6">
                    <ul class="list-unstyled mb-0">
                      <li class="d-flex mb-4 align-items-center">
                        <p class="mb-0 fw-medium me-2 website-analytics-text-bg"><i
                            class="icon-base ti tabler-check text-success"></i></p>
                        <p class="mb-0">Analisis</p>
                      </li>
                      <li class="d-flex align-items-center">
                        <p class="mb-0 fw-medium me-2 website-analytics-text-bg"><i
                            class="icon-base ti tabler-check text-success"></i></p>
                        <p class="mb-0">Keamanan</p>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                <img src="{{ asset('assets/img/illustrations/service-feature-2.png') }}" alt="Prioritas Tiket"
                  height="150" class="card-website-analytics-img" />
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
    {{-- RATA-RATA TIKET PER BULAN --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h5 class="mb-3 card-title">Rata-rata Tiket/Bulan</h5>
          <p class="mb-0 text-body">6 Bulan Terakhir</p>
          <h4 class="mb-0">{{ $rata_rata_per_bulan }}</h4>
        </div>
        <div class="card-body px-0">
          <div id="averageTicketsPerMonth"></div>
        </div>
      </div>
    </div>
    {{-- VERSUS TIKET (SELESAI VS BELUM SELESAI) --}}
    <div class="col-xl-3 col-sm-6">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex justify-content-between">
            <p class="mb-0 text-body">Status Tiket</p>
            <p class="card-text fw-medium text-success">{{ $persentase_selesai }}%</p>
          </div>
          <h4 class="card-title mb-1">{{ $total_tiket }} Tiket</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-4">
              <div class="d-flex gap-2 align-items-center mb-2">
                <span class="badge bg-label-success p-1 rounded">
                  <i class="icon-base ti tabler-checklist"></i>
                </span>
                <p class="mb-0">Selesai</p>
              </div>
              <h5 class="mb-0 pt-1">{{ $persentase_selesai }}%</h5>
              <small class="text-body-secondary">{{ $tiket_selesai }} tiket</small>
            </div>
            <div class="col-4">
              <div class="divider divider-vertical">
                <div class="divider-text">
                  <span class="badge-divider-bg bg-label-secondary">VS</span>
                </div>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
                <p class="mb-0">Belum</p>
                <span class="badge bg-label-warning p-1 rounded"><i class="icon-base ti tabler-ticket-off"></i></span>
              </div>
              <h5 class="mb-0 pt-1">{{ $persentase_belum_selesai }}%</h5>
              <small class="text-body-secondary">{{ $belumSelesai }} tiket</small>
            </div>
          </div>
          <div class="d-flex align-items-center mt-6">
            <div class="progress w-100" style="height: 10px;">
              <div class="progress-bar bg-success" style="width: {{ $persentase_selesai }}%" role="progressbar"
                aria-valuenow="{{ $persentase_selesai }}" aria-valuemin="0" aria-valuemax="100"></div>
              <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $persentase_belum_selesai }}%"
                aria-valuenow="{{ $persentase_belum_selesai }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- LAPORAN MINGGUAN --}}
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header pb-0 d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Laporan Mingguan</h5>
            <p class="card-subtitle">7 Hari Terakhir</p>
          </div>
        </div>
        <div class="card-body">
          <div class="row align-items-center g-md-8">
            <div class="col-12 col-md-5 d-flex flex-column">
              <div class="d-flex gap-2 align-items-center mb-3 flex-wrap">
                <h2 class="mb-0">{{ $total_tiket }}</h2>
                <div class="badge rounded bg-label-primary">Total Tiket</div>
              </div>
              <small class="text-body">Total tiket yang Anda ajukan minggu ini</small>
            </div>
            <div class="col-12 col-md-7 ps-xl-8">
              <div id="weeklyTicketReport"></div>
            </div>
          </div>
          <div class="border rounded p-5 mt-5">
            <div class="row gap-4 gap-sm-0">
              <div class="col-12 col-sm-4">
                <div class="d-flex gap-2 align-items-center">
                  <div class="badge rounded bg-label-success p-1"><i class="icon-base ti tabler-checkbox icon-md"></i>
                  </div>
                  <h6 class="mb-0 fw-normal">Selesai</h6>
                </div>
                <h4 class="my-2">{{ $tiket_selesai }}</h4>
                <div class="progress w-75" style="height:4px">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persentase_selesai }}%"
                    aria-valuenow="{{ $persentase_selesai }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="col-12 col-sm-4">
                <div class="d-flex gap-2 align-items-center">
                  <div class="badge rounded bg-label-warning p-1"><i class="icon-base ti tabler-progress icon-md"></i>
                  </div>
                  <h6 class="mb-0 fw-normal">Proses</h6>
                </div>
                <h4 class="my-2">{{ $belumSelesai }}</h4>
                <div class="progress w-75" style="height:4px">
                  <div class="progress-bar bg-warning" role="progressbar"
                    style="width: {{ $persentase_belum_selesai }}%" aria-valuenow="{{ $persentase_belum_selesai }}"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="col-12 col-sm-4">
                <div class="d-flex gap-2 align-items-center">
                  <div class="badge rounded bg-label-info p-1"><i class="icon-base ti tabler-alarm-average icon-md"></i>
                  </div>
                  <h6 class="mb-0 fw-normal">Avg. Response</h6>
                </div>
                <h4 class="my-2">{{ $avg_response_time }} hari</h4>
                <div class="progress w-75" style="height:4px">
                  <div class="progress-bar bg-info" role="progressbar" style="width: 65%" aria-valuenow="65"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- TRACKER TIKET --}}
    <div class="col-12 col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Tracker Tiket</h5>
            <p class="card-subtitle">Status Penyelesaian</p>
          </div>
        </div>
        <div class="card-body row">
          <div class="col-12 col-sm-4">
            <div class="mt-lg-4 mt-lg-2 mb-lg-6 mb-2">
              <h2 class="mb-0">{{ $total_tiket }}</h2>
              <p class="mb-0">Total Tiket</p>
            </div>
            <ul class="p-0 m-0">
              <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                <div class="badge rounded bg-label-primary p-1_5"><i class="icon-base ti tabler-ticket icon-md"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-nowrap">Tiket Baru</h6>
                  <small
                    class="text-body-secondary">{{ $data_tiket->where('statusAkhir.status', 'Diajukan_oleh_Pemohon')->count() }}</small>
                </div>
              </li>
              <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                <div class="badge rounded bg-label-success p-1_5"><i class="icon-base ti tabler-checklist icon-md"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-nowrap">Tiket Selesai</h6>
                  <small class="text-body-secondary">{{ $tiket_selesai }}</small>
                </div>
              </li>
              <li class="d-flex gap-4 align-items-center pb-1">
                <div class="badge rounded bg-label-warning p-1_5"><i class="icon-base ti tabler-clock icon-md"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-nowrap">Waktu Respon</h6>
                  <small class="text-body-secondary">{{ $avg_response_time }} Hari</small>
                </div>
              </li>
            </ul>
          </div>
          <div class="col-12 col-md-8">
            <div id="ticketCompletionTracker"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
