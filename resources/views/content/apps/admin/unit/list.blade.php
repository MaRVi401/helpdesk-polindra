@extends('layouts/layoutMaster')

@section('title', 'Daftar Unit')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/unit.js', 'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

@section('content')
  {{-- TABEL DAFTAR UNIT --}}
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Daftar Unit</h5>
    </div>
    <div class="card-datatable table-responsive">
      <table class="datatables-unit table">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>Nama Unit</th>
            <th>Kepala Unit</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data_unit as $unit)
            <tr>
              <td></td>
              <td></td>
              <td>{{ $unit->nama_unit }}</td>
              <td>{{ $unit->kepalaUnit->user->name ?? 'Belum Ditentukan' }}</td>
              <td data-id="{{ $unit->id }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Success message dari session --}}
  @if (session('success'))
    <script>
      window.unitSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  {{-- Error message dari session --}}
  @if (session('error'))
    <script>
      window.unitErrorMessage = "{{ session('error') }}";
    </script>
  @endif
@endsection
