@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Kepala Unit')

@section('content')
<div class="row">

  <div class="col-12 mb-4">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Selamat Datang, {{ Auth::user()->nama }}! 👋</h5>
            <p class="mb-4">Anda login sebagai Kepala Unit. Anda dapat melihat dan mengelola semua tiket yang masuk.</p>
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

  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Filter Tiket</h5>
        <form action="{{ route('kepala_unit.dashboard') }}" method="GET">
          <div class="row g-3">
            <div class="col-md-3">
              <label for="id_unit" class="form-label">Unit</label>
              <select class="form-select" id="id_unit" name="id_unit">
                <option value="">Semua Unit</option>
                @foreach($units as $unit)
                  <option value="{{ $unit->id }}" {{ request('id_unit') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->nama_unit }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status">
                <option value="">Semua Status</option>
                <option value="Dibuka" {{ request('status') == 'Dibuka' ? 'selected' : '' }}>Dibuka</option>
                <option value="Sedang Dikerjakan" {{ request('status') == 'Sedang Dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                <option value="Ditutup" {{ request('status') == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="prioritas" class="form-label">Prioritas</label>
              <select class="form-select" id="prioritas" name="prioritas">
                <option value="">Semua Prioritas</option>
                <option value="Rendah" {{ request('prioritas') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                <option value="Sedang" {{ request('prioritas') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="Tinggi" {{ request('prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-primary me-2">Cari</button>
              <a href="{{ route('kepala_unit.dashboard') }}" class="btn btn-secondary">Reset</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <h5 class="card-header">Daftar Tiket</h5>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No. Tiket</th>
              <th>Judul</th>
              <th>Unit</th>
              <th>Pemohon</th>
              <th>Layanan</th>
              <th>Status</th>
              <th>Prioritas</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($tiket as $tkt)
            <tr>
              <td>
                <span class="fw-medium">{{ $tkt->no_tiket }}</span>
              </td>
              
              {{-- !! INI PERBAIKANNYA !! --}}
              <td>{{ \Illuminate\Support\Str::limit($tkt->judul, 30) }}</td>
              
              <td>{{ $tkt->unit?->nama_unit ?? 'N/A' }}</td>
              <td>{{ $tkt->mahasiswa?->user?->nama ?? 'N/A' }}</td>
              <td>{{ $tkt->layanan?->nama_layanan ?? 'N/A' }}</td>
              <td>
                @if ($tkt->status == 'Dibuka')
                  <span class="badge bg-label-info">{{ $tkt->status }}</span>
                @elseif ($tkt->status == 'Sedang Dikerjakan')
                  <span class="badge bg-label-warning">{{ $tkt->status }}</span>
                @elseif ($tkt->status == 'Ditutup')
                  <span class="badge bg-label-danger">{{ $tkt->status }}</span>
                @elseif ($tkt->status == 'Selesai')
                  <span class="badge bg-label-success">{{ $tkt->status }}</span>
                @endif
              </td>
              <td>
                @if ($tkt->prioritas == 'Rendah')
                  <span class="badge bg-label-secondary">{{ $tkt->prioritas }}</span>
                @elseif ($tkt->prioritas == 'Sedang')
                  <span class="badge bg-label-warning">{{ $tkt->prioritas }}</span>
                @elseif ($tkt->prioritas == 'Tinggi')
                  <span class="badge bg-label-danger">{{ $tkt->prioritas }}</span>
                @endif
              </td>
              <td>
                <a href="{{ route('kepala_unit.tiket.show', $tkt->id) }}" class="btn btn-sm btn-primary">Detail</a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center">Tidak ada tiket yang ditemukan.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $tiket->links() }}
      </div>
    </div>
  </div>
  
</div>
@endsection