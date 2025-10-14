@extends('layouts/layoutMaster')

@section('title', 'FAQ')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/app-faq-list.js'])
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
            <th>Aksi</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>

@endsection
