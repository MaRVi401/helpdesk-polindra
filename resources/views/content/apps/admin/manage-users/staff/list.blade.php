@extends('layouts/layoutMaster')

@section('title', 'Daftar Staf')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/staff.js'])
@endsection

@section('content')
  {{-- TABEL DAFTAR STAF --}}
  <div class="card">
    <div class="card-datatable table-responsive pt-0">
      <table class="datatables-basic table">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>NIK</th>
            <th>Email</th>
            <th>Unit</th>
            <th>Jabatan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data_staff as $staff)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>{{ $staff->user->name ?? '-' }}</td>
              <td>{{ $staff->nik }}</td>
              <td>{{ $staff->user->email ?? '-' }}</td>
              <td>{{ $staff->unit->nama_unit ?? '-' }}</td>
              <td>{{ $staff->jabatan->nama_jabatan ?? '-' }}</td>
              <td data-id="{{ $staff->id }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  {{-- MODAL TAMBAH STAFF --}}
  <div class="offcanvas offcanvas-end" id="add-new-record">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title">Tambah Staff</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
      <form class="add-new-record pt-0 row g-2" id="form-add-new-record" action="{{ route('staff.store') }}"
        method="POST">
        @csrf
        {{-- NAMA LENGKAP --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="name">Nama Lengkap</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-user"></i></span>
            <input type="text" id="name" class="form-control dt-full-name" name="name"
              placeholder="Nama Lengkap" aria-label="Nama Lengkap" />
          </div>
        </div>
        {{-- NIK --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="nik">NIK</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-id"></i></span>
            <input type="text" id="nik" name="nik" class="form-control dt-nik" placeholder="NIK"
              aria-label="NIK" />
          </div>
        </div>
        {{-- EMAIL --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="email">Email</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-mail"></i></span>
            <input type="email" id="email" name="email" class="form-control dt-email"
              placeholder="email@polindra.ac.id" aria-label="email@polindra.ac.id" />
          </div>
        </div>
        {{-- ROLE --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="role">Role</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-shield"></i></span>
            <select id="role" name="role" class="form-select dt-role" required>
              <option value="">Pilih Role</option>
              <option value="admin_unit">Admin Unit</option>
              <option value="kepala_unit">Kepala Unit</option>
            </select>
          </div>
        </div>
        {{-- UNIT --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="unit_id">Unit</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-building-community"></i></span>
            <select id="unit_id" name="unit_id" class="form-select dt-unit" required>
              <option value="">Pilih Unit</option>
              @foreach ($data_units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- JABATAN --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="jabatan_id">Jabatan</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-briefcase"></i></span>
            <select id="jabatan_id" name="jabatan_id" class="form-select dt-jabatan" required>
              <option value="">Pilih Jabatan</option>
              @foreach ($data_jabatan as $jabatan)
                <option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1">Simpan</button>
        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Batal</button>
      </form>
    </div>
  </div>

  @if (session('success'))
    <script>
      window.staffSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  @if (session('error'))
    <script>
      window.staffErrorMessage = "{{ session('error') }}";
    </script>
  @endif

  @if ($errors->any())
    <script>
      window.staffErrorMessage = "{{ $errors->first() }}";
    </script>
  @endif

@endsection
