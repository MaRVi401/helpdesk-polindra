@extends('layouts/layoutMaster')

@section('title', 'FAQ')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/faq-list.js', 'resources/assets/js/extended-ui-sweetalert2.js'])

@endsection

@section('content')
  <!-- Product List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Frequently Asked Questions (FAQ)</h5>
      <div class="col-md-4 faq_status"></div>
      <div class="col-md-4 judul"></div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="datatables-faq table">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th>Judul</th>
            <th>Layanan</th>
            <th>Status</th>
            <th>Pembuat</th>
            <th>Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data_faq as $faq)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>{{ $faq->judul }}</td>
              <td>{{ $faq->layanan->nama ?? 'N/A' }}</td>
              <td>{{ $faq->status }}</td>
              <td>{{ $faq->user->name ?? 'Unknown' }}</td>
              <td>{{ $faq->created_at }}</td>
              <td data-id="{{ $faq->id }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Success message dari session --}}
  @if (session('success'))
    <script>
      window.faqSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  @if (session('error'))
    <script>
      window.faqErrorMessage = "{{ session('error') }}";
    </script>
  @endif
@endsection
