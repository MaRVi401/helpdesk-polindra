@extends('layouts/layoutMaster')

@section('title', 'Detail Unit')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail Unit</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Unit</label>
        <p class="text-muted mb-0">{{ $data_unit->nama_unit }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Kepala Unit</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">{{ $data_unit->kepalaUnit->user->name ?? 'Belum Ditentukan' }}</span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Dibuat pada</label>
        <p class="text-muted mb-0">{{ $data_unit->created_at->format('d M Y H:i') }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Diperbarui pada</label>
        <p class="text-muted mb-0">{{ $data_unit->updated_at->format('d M Y H:i') }}</p>
      </div>
      <a href="{{ route('unit.index') }}" class="btn btn-outline-secondary mt-3">
        <i class="icon-base ti tabler-arrow-left me-1"></i>
        Kembali
      </a>
    </div>
  </div>
@endsection
