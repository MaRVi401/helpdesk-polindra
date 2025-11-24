@extends('layouts/layoutMaster')

@section('title', 'Daftar Tiket Masuk - Kepala Unit')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    $('.datatables-tiket').DataTable();
    $('.select2').select2({
        dropdownParent: $('.modal') // Fix select2 inside modal
    });
    
    // Handle passing ID to modal
    $('.btn-assign').on('click', function() {
        var tiketId = $(this).data('id');
        var actionUrl = "{{ route('kepala-unit.tiket.assign-pic', ':id') }}";
        actionUrl = actionUrl.replace(':id', tiketId);
        $('#formAssignPic').attr('action', actionUrl);
    });
  });
</script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Kepala Unit /</span> Daftar Tiket Masuk
</h4>

<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Tiket yang Ditugaskan ke Anda ({{ Auth::user()->name }})</h5>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-tiket table border-top">
      <thead>
        <tr>
          <th>No Tiket</th>
          <th>Tanggal</th>
          <th>Judul</th>
          <th>Mahasiswa</th>
          <th>Layanan</th>
          <th>Status</th>
          <th>PIC Saat Ini</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tickets as $tiket)
        <tr>
            <td>#{{ $tiket->id }}</td>
            <td>{{ $tiket->created_at->format('d M Y') }}</td>
            <td>{{ Str::limit($tiket->judul, 30) }}</td>
            <td>
                <div class="d-flex justify-content-start align-items-center user-name">
                    <div class="d-flex flex-column">
                        <span class="fw-medium">{{ $tiket->mahasiswa->user->name ?? '-' }}</span>
                        <small class="text-muted">{{ $tiket->mahasiswa->nim ?? '-' }}</small>
                    </div>
                </div>
            </td>
            <td>{{ $tiket->layanan->nama_layanan ?? '-' }}</td>
            <td>
                <span class="badge bg-label-{{ $tiket->status_tiket->warna ?? 'primary' }}">
                    {{ $tiket->status_tiket->nama_status ?? 'N/A' }}
                </span>
            </td>
            <td>
                {{ $tiket->pic->user->name ?? 'Belum ada PIC' }}
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <a href="{{ route('kepala-unit.tiket.show', $tiket->id) }}" class="text-body" data-bs-toggle="tooltip" title="Lihat Detail">
                        <i class="ti ti-eye ti-sm mx-2"></i>
                    </a>
                    <button type="button" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill btn-assign" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalAssignPic"
                            data-id="{{ $tiket->id }}"
                            title="Atur PIC">
                        <i class="ti ti-user-check ti-sm"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Assign PIC -->
<div class="modal fade" id="modalAssignPic" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Atur Admin Unit (PIC)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAssignPic" method="POST" action="">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col mb-3">
              <label for="pic_id" class="form-label">Pilih Staff / PIC</label>
              <select id="pic_id" name="pic_id" class="select2 form-select" required>
                <option value="">Pilih PIC...</option>
                @foreach($potentialPics as $staff)
                    <option value="{{ $staff->user->id }}">{{ $staff->user->name }} ({{ $staff->jabatan->nama_jabatan ?? 'Staff' }})</option>
                @endforeach
              </select>
              <div class="form-text">Pilih Admin Unit yang akan menangani tiket ini.</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan PIC</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection