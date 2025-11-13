@extends('layouts/layoutMaster')

@section('title', 'Detail Program Studi')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail Program Studi</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Program Studi</label>
        <p class="text-muted mb-0">{{ $data_program_studi->program_studi }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Jurusan</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">{{ $data_program_studi->jurusan->nama_jurusan }}</span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Dibuat pada</label>
        <p class="text-muted mb-0">{{ $data_program_studi->created_at->format('d M Y H:i') }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Diperbarui pada</label>
        <p class="text-muted mb-0">{{ $data_program_studi->updated_at->format('d M Y H:i') }}</p>
      </div>
      <a href="{{ route('major.index') }}" class="btn btn-outline-secondary mt-3">
        <i class="icon-base ti tabler-arrow-left me-1"></i>
        Kembali
      </a>
    </div>
  </div>
@endsection
