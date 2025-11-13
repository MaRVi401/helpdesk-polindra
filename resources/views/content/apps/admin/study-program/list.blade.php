@extends('layouts/layoutMaster')

@section('title', 'Daftar Program Studi')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/study-program.js', 'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

@section('content')
  {{-- TABEL DAFTAR PROGRAM STUDI --}}
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Daftar Program Studi</h5>
    </div>
    <div class="card-datatable table-responsive">
      <table class="datatables-study-program table">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>Program Studi</th>
            <th>Jurusan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data_program_studi as $prodi)
            <tr>
              <td></td>
              <td></td>
              <td>{{ $prodi->program_studi }}</td>
              <td>{{ $prodi->jurusan->nama_jurusan ?? '-' }}</td>
              <td data-id="{{ $prodi->id }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Success message dari session --}}
  @if (session('success'))
    <script>
      window.studyProgramSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  {{-- Error message dari session --}}
  @if (session('error'))
    <script>
      window.studyProgramErrorMessage = "{{ session('error') }}";
    </script>
  @endif
@endsection
