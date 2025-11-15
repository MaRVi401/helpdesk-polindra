@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('content')
  @if (session('success'))
    <div class="bs-toast toast fade show w-100 mb-3" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <i class="icon-base ti tabler-user-check icon-xs me-2 text-primary"></i>
        <div class="me-auto fw-medium">Selamat datang</div>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">{{ session('success') }}</div>
    </div>
  @endif
  <h4>Dashboard</h4>
@endsection
