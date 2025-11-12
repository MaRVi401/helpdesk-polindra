@extends('layouts/layoutMaster')

@section('title', 'Detail Staf')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail Data Staf</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <p class="form-control">{{ $data_staff->user->name ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">NIK</label>
        <p class="form-control">{{ $data_staff->nik }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <p class="form-control">{{ $data_staff->user->email ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <p class="form-control">
          @if ($data_staff->user)
            {{ ucwords(str_replace('_', ' ', $data_staff->user->role)) }}
          @else
            -
          @endif
        </p>
      </div>
      <div class="mb-3">
        <label class="form-label">Unit</label>
        <p class="form-control">{{ $data_staff->unit->nama_unit ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Jabatan</label>
        <p class="form-control">{{ $data_staff->jabatan->nama_jabatan ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Data akun dibuat</label>
        <p class="form-control">{{ $data_staff->created_at->format('d M Y H:i') }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Data akun diperbarui</label>
        <p class="form-control">{{ $data_staff->updated_at->format('d M Y H:i') }}</p>
      </div>
      <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary mt-3">Kembali</a>
    </div>
  </div>
@endsection
