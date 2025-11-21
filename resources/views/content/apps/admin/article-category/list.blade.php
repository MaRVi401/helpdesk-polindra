@extends('layouts/layoutMaster')

@section('title', 'Kategori Artikel')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/article-category.js', 'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

@section('content')
  {{-- TABEL DAFTAR KATEGORI ARTIKEL --}}
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Kategori Artikel</h5>
    </div>
    <div class="card-datatable table-responsive">
      <table class="datatables-basic table">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th>Kategori</th>
            <th>Dibuat</th>
            <th>Diperbarui</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data_kategori as $kategori)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>{{ $kategori->kategori }}</td>
              <td>{{ $kategori->created_at->format('d M Y H:i') }}</td>
              <td>{{ $kategori->updated_at->format('d M Y H:i') }}</td>
              <td data-id="{{ $kategori->id }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Success message dari session --}}
  @if (session('success'))
    <script>
      window.articleCategorySuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  @if (session('error'))
    <script>
      window.articleCategoryErrorMessage = "{{ session('error') }}";
    </script>
  @endif
@endsection
