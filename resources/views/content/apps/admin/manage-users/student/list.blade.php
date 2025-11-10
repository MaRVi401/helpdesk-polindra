@extends('layouts/layoutMaster')

@section('title', 'Daftar Mahasiswa')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/student.js'])
@endsection

@section('content')
  {{-- TABEL DAFTAR MAHASISWA --}}
  <div class="card">
    <div class="card-datatable table-responsive pt-0">
      <table class="datatables-basic table">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>NIM</th>
            <th>Email</th>
            <th>Prodi</th>
            <th>Tahun Masuk</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data_mahasiswa as $mahasiswa)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>{{ $mahasiswa->user->name }}</td>
              <td>{{ $mahasiswa->nim }}</td>
              <td>{{ $mahasiswa->user->email }}</td>
              <td>{{ $mahasiswa->programStudi->program_studi }}</td>
              <td>{{ $mahasiswa->tahun_masuk }}</td>
              <td data-id="{{ $mahasiswa->id }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  {{-- MODAL --}}
  <div class="offcanvas offcanvas-end" id="add-new-record">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title">Tambah Mahasiswa</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
      <form class="add-new-record pt-0 row g-2" action="{{ route('student.store') }}" method="POST">
        @csrf
        {{-- NAMA LENGKAP --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="name">Nama Lengkap</label>
          <div class="input-group input-group-merge">
            <span id="basicFullname2" class="input-group-text"><i class="icon-base ti tabler-user"></i></span>
            <input type="text" id="name" class="form-control dt-full-name" name="name"
              placeholder="Nama Lengkap" aria-label="Nama Lengkap" />
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        {{-- NIM --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="nim">NIM</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-id"></i></span>
            <input type="text" id="nim" name="nim" class="form-control dt-nim" placeholder="NIM"
              aria-label="NIM" />
            @error('nim')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        {{-- EMAIL --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="email">Email</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-mail"></i></span>
            <input type="text" id="email" name="email" class="form-control dt-email"
              placeholder="nim@student.polindra.ac.id" aria-label="nim@student.polindra.ac.id" />
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        {{-- PROGRAM STUDI --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="program_studi">Program Studi</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-notebook"></i></span>
            <select id="program_studi" name="program_studi" class="form-select dt-prodi" required>
              <option value="">Pilih Program Studi</option>
              @foreach ($data_program_studi as $jurusan => $prodiGroup)
                <optgroup label="{{ $jurusan }}">
                  @foreach ($prodiGroup as $prodi)
                    <option value="{{ $prodi->id }}">{{ $prodi->program_studi }}</option>
                  @endforeach
                </optgroup>
              @endforeach
            </select>
            @error('program_studi')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        {{-- TAHUN MASUK --}}
        <div class="col-sm-12 form-control-validation">
          <label class="form-label" for="tahun_masuk">Tahun Masuk</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text">
              <i class="icon-base ti tabler-calendar"></i>
            </span>
            <select id="tahun_masuk" name="tahun_masuk" class="form-control dt-tahun-masuk" required>
              <option value="" disabled selected>Pilih Tahun Masuk</option>
              <option value="2022">2022</option>
              <option value="2023">2023</option>
              <option value="2024">2024</option>
              <option value="2025">2025</option>
            </select>
            @error('tahun_masuk')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1">Simpan</button>
        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Batal</button>
      </form>
    </div>
  </div>
  @if (session('success'))
    <script>
      window.studentSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  @if (session('error'))
    <script>
      window.studentErrorMessage = "{{ session('error') }}";
    </script>
  @endif

  @if ($errors->any())
    <script>
      // Show the first validation error using the same Swal flow
      window.studentErrorMessage = "{{ $errors->first() }}";
    </script>
  @endif

@endsection
