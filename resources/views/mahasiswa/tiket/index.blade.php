@extends('layouts.layoutMaster')

@section('title', 'Daftar Tiket')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<style>
    .badge-status { padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #fff; white-space: nowrap; }
    
    /* --- CSS Status Badge (Sama dengan Detail) --- */
    /* Abu-abu */
    .status-diajukan_oleh_pemohon { background-color: #a0aec0; }

    /* Kuning */
    .status-ditangani_oleh_pic,
    .status-diselesaikan_oleh_pic { background-color: #f6ad55; }

    /* Merah */
    .status-dinilai_belum_selesai_oleh_pemohon,
    .status-pemohon_bermasalah { background-color: #f56565; }

    /* Hijau */
    .status-dinilai_selesai_oleh_kepala,
    .status-dinilai_selesai_oleh_pemohon { background-color: #48bb78; }
</style>
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
            <th>No. Tiket</th>
            <th>Layanan</th>
            <th>Status</th>
            <th>Prioritas</th>
            <th>Tgl Dibuat</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @forelse ($tikets as $tiket)
          <tr>
            <td>
              <a href="{{ route('mahasiswa.tiket.show', $tiket->id) }}">
                <strong>#{{ $tiket->no_tiket }}</strong>
              </a>
            </td>
            
            <td>
                {{ $tiket->layanan->nama ?? '-' }} 
                <br>
                <small class="text-muted">({{ $tiket->layanan->unit->nama_unit ?? 'N/A' }})</small>
            </td>
            
            <td>
                @php
                    $statusTerakhir = $tiket->riwayatStatus->sortByDesc('created_at')->first()->status ?? 'Diajukan_oleh_Pemohon';
                    // Konversi ke nama class (snake_case)
                    $statusClass = 'status-' . strtolower($statusTerakhir);
                @endphp
                <span class="badge-status {{ $statusClass }}">
                    {{ str_replace('_', ' ', $statusTerakhir) }}
                </span>
            </td>
            
            <td>
              @php
                  $prioVal = $tiket->layanan->prioritas ?? 0;
                  $prioBadge = 'secondary';
                  $prioText = '-';

                  if($prioVal == 1) { $prioBadge = 'success'; $prioText = 'Rendah'; }
                  if($prioVal == 2) { $prioBadge = 'warning'; $prioText = 'Sedang'; }
                  if($prioVal == 3) { $prioBadge = 'danger'; $prioText = 'Tinggi'; }
              @endphp
              <span class="badge bg-label-{{ $prioBadge }}">{{ $prioText }}</span>
            </td>

            <td>{{ $tiket->created_at->format('d M Y, H:i') }}</td>
            
            <td>
              <div class="d-flex flex-column gap-2">
                  <a href="{{ route('mahasiswa.tiket.show', $tiket->id) }}" class="btn btn-sm btn-info w-100">
                    Detail
                  </a>
                  <form action="{{ route('mahasiswa.tiket.destroy', $tiket->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tiket ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger w-100">
                      Hapus
                    </button>
                  </form>
              </div>
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