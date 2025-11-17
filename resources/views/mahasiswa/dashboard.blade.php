@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="row">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Selamat Datang, {{ Auth::user()->nama }}! 👋</h5>
            <p class="mb-4">Ini adalah halaman dashboard Anda. Buat tiket baru atau lihat status tiket Anda saat ini.</p>
            <a href="{{ route('mahasiswa.tiket.create') }}" class="btn btn-sm btn-primary">Buat Tiket Baru</a>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block fw-medium mb-1">Total Tiket</span>
        <h3 class="card-title mb-2">{{ $totalTiket }}</h3>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block fw-medium mb-1">Tiket Dibuka</span>
        <h3 class="card-title mb-2">{{ $tiketDibuka }}</h3>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block fw-medium mb-1">Tiket Dikerjakan</span>
        <h3 class="card-title mb-2">{{ $tiketDikerjakan }}</h3>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block fw-medium mb-1">Tiket Selesai</span>
        <h3 class="card-title mb-2">{{ $tiketSelesai }}</h3>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">5 Tiket Terbaru Anda</h5>
        <a href="{{ route('mahasiswa.tiket.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua Tiket</a>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No. Tiket</th>
              <th>Judul</th>
              <th>Layanan</th>
              <th>Unit</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($tiket as $tkt)
            <tr>
              <td><span class="fw-medium">{{ $tkt->no_tiket }}</span></td>
              <td>{{ \Illuminate\Support\Str::limit($tkt->judul, 40) }}</td>
              <td>{{ $tkt->layanan?->nama_layanan ?? 'N/A' }}</td>
              <td>{{ $tkt->unit?->nama_unit ?? 'N/A' }}</td>
              <td>
                @if ($tkt->status == 'Dibuka')
                  <span class="badge bg-label-info">{{ $tkt->status }}</span>
                @elseif ($tkt->status == 'Sedang Dikerjakan')
                  <span class="badge bg-label-warning">{{ $tkt->status }}</span>
                @elseif ($tkt->status == 'Ditutup')
                  <span class="badge bg-label-danger">{{ $tkt->status }}</span>
                @elseif ($tkt->status == 'Selesai')
                  <span class="badge bg-label-success">{{ $tkt->status }}</span>
                @else
                  <span class="badge bg-label-secondary">{{ $tkt->status }}</span>
                @endif
              </td>
              <td>
                <a href="{{ route('mahasiswa.tiket.show', $tkt->id) }}" class="btn btn-sm btn-primary">Detail</a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center">Anda belum memiliki tiket.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if ($tiket->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $tiket->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection