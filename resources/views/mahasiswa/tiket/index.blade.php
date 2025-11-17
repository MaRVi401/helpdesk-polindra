@extends('layouts/layoutMaster')

@section('title', 'Daftar Tiket')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script>
  $(function () {
    $('#tiket-table').DataTable({
      responsive: true,
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
      },
      "order": [[ 4, "desc" ]]
    });
  });
</script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Tiket /</span> Daftar Tiket Saya
</h4>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daftar Tiket Anda</h5>
    <a href="{{ route('mahasiswa.tiket.create') }}" class="btn btn-primary">
      <i class="ti ti-plus me-1"></i> Buat Tiket Baru
    </a>
  </div>
  <div class="card-body">
    @if(session('success'))
    <div class="alert alert-success" role="alert">
      {{ session('success') }}
    </div>
    @endif
    <div class="table-responsive text-nowrap">
      <table class="table table-striped" id="tiket-table">
        <thead>
          <tr>
            {{-- PERBAIKAN: Ganti Judul -> No. Tiket --}}
            <th>No. Tiket</th>
            <th>Layanan</th>
            <th>Status</th>
            <th>Prioritas</th>
            <th>Tgl Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @forelse ($tikets as $tiket)
          <tr>
            <td>
              <a href="{{ route('mahasiswa.tiket.show', $tiket->id) }}">
                {{-- PERBAIKAN: Ganti judul -> no_tiket --}}
                <strong>{{ $tiket->no_tiket }}</strong>
              </a>
            </td>
            {{-- Tampilkan nama layanan dan unitnya --}}
            <td>{{ $tiket->layanan->nama }} ({{ $tiket->layanan->unit->nama_unit ?? '' }})</td>
            <td>
              @if($tiket->status == 'menunggu')
                <span class="badge bg-label-warning">Menunggu</span>
              @elseif($tiket->status == 'diproses')
                <span class="badge bg-label-info">Diproses</span>
              @elseif($tiket->status == 'selesai')
                <span class="badge bg-label-success">Selesai</span>
              @elseif($tiket->status == 'ditutup')
                <span class="badge bg-label-secondary">Ditutup</span>
              @endif
            </td>
            <td>
              @if($tiket->prioritas == 'rendah')
                <span class="badge bg-label-secondary">Rendah</span>
              @elseif($tiket->prioritas == 'sedang')
                <span class="badge bg-label-warning">Sedang</span>
              @elseif($tiket->prioritas == 'tinggi')
                <span class="badge bg-label-danger">Tinggi</span>
              @endif
            </td>
            <td>{{ $tiket->created_at->format('d M Y, H:i') }}</td>
            <td>
              <a href="{{ route('mahasiswa.tiket.show', $tiket->id) }}" class="btn btn-sm btn-info">Detail</a>
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
  </div>
</div>
@endsection