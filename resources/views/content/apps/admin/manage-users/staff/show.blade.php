@extends('layouts/layoutMaster')

@section('title', 'Detail Staf')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail Staf</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Lengkap</label>
        <p class="text-muted mb-0">{{ $data_staff->user->name ?? '-' }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">NIK (Nomor Induk Kependudukan)</label>
        <p class="text-muted mb-0">{{ $data_staff->nik }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <p class="text-muted mb-0">{{ $data_staff->user->email ?? '-' }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Role</label>
        <p class="text-muted mb-0">
          @if ($data_staff->user)
            <span class="badge bg-label-primary"> {{ ucwords(str_replace('_', ' ', $data_staff->user->role)) }}</span>
          @else
            -
          @endif
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Unit</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">{{ $data_staff->unit->nama_unit ?? '-' }}</span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Jabatan</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">{{ $data_staff->jabatan->nama_jabatan ?? '-' }}</span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Dibuat pada</label>
        <p class="text-muted mb-0">{{ $data_staff->created_at->format('d M Y H:i') }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Diperbarui pada</label>
        <p class="text-muted mb-0">{{ $data_staff->updated_at->format('d M Y H:i') }}</p>
      </div>
      <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary mt-3">
        <i class="icon-base ti tabler-arrow-left me-1"></i>
        Kembali
      </a>
    </div>
  </div>
@endsection
