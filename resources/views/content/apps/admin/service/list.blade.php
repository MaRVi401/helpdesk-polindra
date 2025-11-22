@extends('layouts/layoutMaster')

@section('title', 'Daftar Layanan')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/service.js'])
@endsection

@section('content')
  {{-- TABEL UNIT LAYANAN --}}
  @foreach ($data_layanan as $nama_unit => $layananList)
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          Daftar Layanan & PIC - {{ $nama_unit }}
        </h5>
      </div>
      <div class="card-datatable table-responsive">
        <table class="datatables-basic table border-top" data-unit="{{ $nama_unit }}">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th>No</th>
              <th>Nama Layanan</th>
              <th>Unit</th>
              <th>PIC</th>
              <th>Prioritas</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($layananList as $layanan)
              <tr data-archived="{{ $layanan->status_arsip }}">
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $layanan->nama }}</td>
                <td>{{ $layanan->unit->nama_unit ?? 'N/A' }}</td>
                <td>
                  @if ($layanan->penanggungJawab->isEmpty())
                    <span class="text-muted">Belum ditentukan</span>
                  @else
                    {{ $layanan->penanggungJawab->pluck('user.name')->filter()->implode(', ') ?: 'N/A' }}
                  @endif
                </td>
                <td>
                  @if ($layanan->prioritas == 1)
                    Rendah
                  @elseif ($layanan->prioritas == 2)
                    Sedang
                  @elseif ($layanan->prioritas == 3)
                    Tinggi
                  @else
                    N/A
                  @endif
                </td>
                <td>
                  @if ($layanan->status_arsip == 1)
                    <span class="badge bg-label-secondary">Diarsipkan</span>
                  @else
                    <span class="badge bg-label-success">Aktif</span>
                  @endif
                </td>
                <td data-id="{{ $layanan->id }}"></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endforeach
  {{-- MODAL TAMBAH LAYANAN --}}
  <div class="offcanvas offcanvas-end" id="add-new-record">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title">Tambah Layanan</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
      <form id="form-add-new-record" class="add-new-record pt-0 row g-3" action="{{ route('service.store') }}"
        method="POST">
        @csrf
        {{-- NAMA LAYANAN --}}
        <div class="col-12 form-control-validation">
          <label class="form-label" for="nama">Nama Layanan</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-layout-2"></i></span>
            <input type="text" id="nama" name="nama" class="form-control dt-nama"
              placeholder="Masukkan Nama Layanan" />
          </div>
          @error('nama')
            <div class="form-text text-danger">{{ $message }}</div>
          @enderror
        </div>
        {{-- UNIT --}}
        <div class="col-12 form-control-validation">
          <label class="form-label" for="unit_id">Unit</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-buildings"></i></span>
            <select id="unit_id" name="unit_id" class="form-select dt-unit">
              <option value="" disabled selected>Pilih Unit</option>
              @foreach ($data_unit as $unit)
                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
              @endforeach
            </select>
          </div>
          @error('unit_id')
            <div class="form-text text-danger">{{ $message }}</div>
          @enderror
        </div>
        {{-- PRIORITAS --}}
        <div class="col-12 form-control-validation">
          <label class="form-label" for="prioritas">Prioritas</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-stack-front"></i></span>
            <select id="prioritas" name="prioritas" class="form-select dt-prioritas">
              <option value="" disabled selected>Pilih Prioritas</option>
              <option value="1">Rendah</option>
              <option value="2">Sedang</option>
              <option value="3">Tinggi</option>
            </select>
          </div>
          @error('prioritas')
            <div class="form-text text-danger">{{ $message }}</div>
          @enderror
        </div>
        {{-- PENANGGUNG JAWAB --}}
        <div class="col-12">
          <label class="form-label" for="penanggung_jawab_ids">Penanggung Jawab (PIC)</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-users"></i></span>
            <select id="penanggung_jawab_ids" name="penanggung_jawab_ids[]" class="form-select dt-penanggung-jawab">
              <option value="" disabled selected>Pilih Penanggung Jawab</option>
              @foreach ($data_staf as $staf)
                <option value="{{ $staf->id }}">{{ $staf->user->name ?? $staf->id }}</option>
              @endforeach
            </select>
          </div>
          @error('penanggung_jawab_ids')
            <div class="form-text text-danger">{{ $message }}</div>
          @enderror
        </div>
        {{-- STATUS LAYANAN --}}
        <div class="col-12 form-control-validation">
          <label class="form-label" for="status_arsip">Status Layanan</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-archive"></i></span>
            <select id="status_arsip" name="status_arsip" class="form-select dt-status-arsip">
              <option value="" disabled>Pilih Status Layanan</option>
              <option value="0" {{ old('status_arsip', '0') == '0' ? 'selected' : '' }}>Aktif</option>
              <option value="1" {{ old('status_arsip') == '1' ? 'selected' : '' }}>Arsipkan</option>
            </select>
          </div>
          @error('status_arsip')
            <div class="form-text text-danger">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2 waves-effect waves-light">
          Simpan
        </button>
        <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="offcanvas">
          Batal
        </button>
      </form>
    </div>
  </div>

  {{-- Success message dari session --}}
  @if (session('success'))
    <script>
      window.serviceSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  @if (session('error'))
    <script>
      window.serviceErrorMessage = "{{ session('error') }}";
    </script>
  @endif
@endsection
