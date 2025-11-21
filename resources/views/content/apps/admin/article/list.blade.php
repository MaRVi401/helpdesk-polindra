@extends('layouts/layoutMaster')

@section('title', 'Daftar Artikel')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/article.js', 'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

@section('content')
  {{-- TABEL DAFTAR ARTIKEL --}}
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Artikel</h5>
      <div class="col-md-4 article_status"></div>
      <div class="col-md-4 judul"></div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="datatables-basic table">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Gambar</th>
            <th>Status</th>
            <th>Penulis</th>
            <th>Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data_artikel as $artikel)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>{{ $artikel->judul }}</td>
              <td>{{ $artikel->kategori->kategori ?? 'N/A' }}</td>
              <td>{{ $artikel->gambar ?? '-' }}</td>
              <td>{{ $artikel->status }}</td>
              <td>{{ $artikel->user->name ?? 'Unknown' }}</td>
              <td>{{ $artikel->created_at }}</td>
              <td data-id="{{ $artikel->id }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Success message dari session --}}
  @if (session('success'))
    <script>
      window.articleSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  @if (session('error'))
    <script>
      window.articleErrorMessage = "{{ session('error') }}";
    </script>
  @endif
@endsection
