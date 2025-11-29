@extends('layouts/layoutMaster')

@section('title', 'Permohonan Layanan Saya')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss'])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/management/service-ticket/mahasiswa.js')
@endsection

@section('content')
  {{-- HEADER --}}
  <div class="row">
    <div class="col-12">
      <div class="card mb-6">
        <div class="user-profile-header-banner">
          <img src="{{ asset('assets/img/pages/profile-banner-mahasiswa.png') }}" alt="Banner image mahasiswa"
            class="rounded-top" />
        </div>
        <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
        </div>
      </div>
    </div>
  </div>
  {{-- STATISTIK LAYANAN --}}
  <div class="row mt-5">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-lg-7 mb-3 mb-lg-0">
              <div class="row gy-3">
                <!-- TOTAL PERMOHONAN -->
                <div class="col-md-4 col-6">
                  <div class="d-flex align-items-center">
                    <div class="badge rounded bg-label-primary me-2 me-md-3 p-1">
                      <i class="icon-base ti tabler-ticket fs-5 fs-md-4"></i>
                    </div>
                    <div class="card-info">
                      <div class="fw-medium text-dark mb-1 small fs-md-6">Total Permohonan</div>
                      <small class="text-muted d-block">{{ $total_tiket }}</small>
                    </div>
                  </div>
                </div>
                <!-- BELUM SELESAI -->
                <div class="col-md-4 col-6">
                  <div class="d-flex align-items-center">
                    <div class="badge rounded bg-label-warning me-2 me-md-3 p-1">
                      <i class="icon-base ti tabler-ticket-off fs-5 fs-md-4"></i>
                    </div>
                    <div class="card-info">
                      <div class="fw-medium text-dark mb-1 small fs-md-6">Belum Selesai</div>
                      <small class="text-muted d-block">{{ $belumSelesai }}</small>
                    </div>
                  </div>
                </div>
                <!-- SELESAI -->
                <div class="col-md-4 col-6">
                  <div class="d-flex align-items-center">
                    <div class="badge rounded bg-label-success me-2 me-md-3 p-1">
                      <i class="icon-base ti tabler-checklist fs-5 fs-md-4"></i>
                    </div>
                    <div class="card-info">
                      <div class="fw-medium text-dark mb-1 small fs-md-6">Tiket Selesai</div>
                      <small class="text-muted d-block">{{ $tiket_selesai }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            {{-- FILTER & SEARCH --}}
            <div class="col-lg-5">
              <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 gap-md-3 justify-content-md-end">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                  <label class="form-label mb-0 text-nowrap small small-md-base">Status Tiket:</label>
                  <select class="form-select form-select-sm" id="status-dropdown" style="min-width: 120px;">
                    <option value="semua">Semua</option>
                    <option value="belum-selesai">Belum Selesai</option>
                    <option value="selesai">Selesai</option>
                  </select>
                </div>
                <div class="flex-grow-1 flex-md-grow-0" style="min-width: 0;">
                  <input type="text" class="form-control form-control-sm w-100" id="search-tiket"
                    placeholder="Pencarian" style="min-width: 120px;">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- CARD TIKET LAYANAN  --}}
  <div class="row g-6 mt-2">
    @forelse($data_tiket as $tiket)
      <div class="col-xl-4 col-lg-6 col-md-6 tiket-card"
        data-status="{{ $tiket->riwayatStatus->sortByDesc('created_at')->first()->status ?? 'Diajukan_oleh_Pemohon' }}"
        data-tiket="{{ $tiket->no_tiket }}" data-judul="{{ $tiket->judul }}"
        data-layanan="{{ $tiket->layanan->nama ?? '' }}">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3 pb-1">
              {{-- NAMA LAYANAN - DETAIL TIKET --}}
              <a href="{{ route('service-ticket.show', $tiket->id) }}" class="d-flex align-items-center">
                <div class="avatar me-2">
                  <div
                    class="avatar-initial bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base ti tabler-layout text-info"></i>
                  </div>
                </div>
                <div class="me-2 text-heading h5 mb-0">{{ $tiket->layanan->nama ?? 'Tidak termuat...' }}</div>
              </a>
              <div class="ms-auto">
                <ul class="list-inline mb-0 d-flex align-items-center">
                  <li class="list-inline-item me-0">
                    <form action="{{ route('service-ticket.destroy', $tiket->id) }}" method="POST"
                      style="display:inline;" class="form-delete-tiket">
                      @csrf
                      @method('DELETE')
                      <button type="button"
                        class="d-flex align-self-center btn btn-icon btn-text-secondary rounded-pill btn-delete-tiket"
                        data-id="{{ $tiket->id }}">
                        <i class="icon-base ti tabler-trash text-body-secondary"></i>
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>
            {{-- TANGGAL DIBUAT --}}
            <div class="mb-2">
              <small class="text-body-secondary">
                Dibuat : {{ $tiket->created_at->format('d M Y, H:i') }}
              </small>
            </div>
            {{-- DESKRISPI --}}
            <p class="mb-3 pb-1 line-clamp-3">{{ $tiket->deskripsi }}</p>
            {{-- INFO TIKET & KOMENTAR --}}
            <div class="d-flex align-items-center gap-4 mb-3">
              <div class="d-flex align-items-center">
                <div class="me-2">
                  <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center p-1">
                    <i class="icon-base ti tabler-ticket text-primary"></i>
                  </div>
                </div>
                <div>
                  <small class="text-secondary d-block">No. Tiket : {{ $tiket->no_tiket }}</small>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <div class="me-2">
                  <div class="bg-label-warning rounded-circle d-flex align-items-center justify-content-center p-1">
                    <i class="icon-base ti tabler-message text-warning"></i>
                  </div>
                </div>
                <div>
                  <small class="text-secondary d-block">{{ $tiket->komentar->count() }} Komentar</small>
                </div>
              </div>
            </div>
            {{-- STATUS LAYANAN --}}
            <div
              class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center border-top pt-3 gap-2">
              <div class="d-flex align-items-center">
                @php
                  $statusTerakhir =
                      $tiket->riwayatStatus->sortByDesc('created_at')->first()->status ?? 'Diajukan_oleh_Pemohon';
                  $statusDisplay = str_replace('_', ' ', $statusTerakhir);
                  // Conditional statement untuk menentukan status
                  if (in_array($statusTerakhir, ['Diajukan_oleh_Pemohon'])) {
                      $statusClass = 'bg-label-secondary';
                  } elseif (in_array($statusTerakhir, ['Ditangani_oleh_PIC', 'Diselesaikan_oleh_PIC'])) {
                      $statusClass = 'bg-label-info';
                  } elseif (in_array($statusTerakhir, ['Dinilai_belum_selesai_oleh_Pemohon', 'Pemohon_bermasalah'])) {
                      $statusClass = 'bg-label-danger';
                  } elseif (
                      in_array($statusTerakhir, ['Dinilai_selesai_oleh_Kepala', 'Dinilai_selesai_oleh_Pemohon'])
                  ) {
                      $statusClass = 'bg-label-info';
                  } else {
                      $statusClass = 'bg-label-success';
                  }
                @endphp
                <span class="badge {{ $statusClass }}">
                  Status : {{ $statusDisplay }}
                </span>
              </div>
              <a href="{{ route('service-ticket.show', $tiket->id) }}" class="btn btn-primary btn-sm">
                Detail Tiket
              </a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info" role="alert">
          Kamu belum memiliki tiket.
        </div>
      </div>
    @endforelse
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
