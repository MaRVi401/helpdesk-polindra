@extends('layouts/layoutMaster')
@section('title', 'Detail Layanan')
@section('content')
  <div class="card">
    <h5 class="card-header">Detail Layanan</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Layanan</label>
        <p class="text-muted mb-0">{{ $data_layanan->nama }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Unit</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">
            {{ $data_layanan->unit->nama_unit ?? '-' }}
          </span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Prioritas</label>
        <p class="text-muted mb-0">
          @php
            $prioritas_label = [
                1 => 'Tinggi',
                2 => 'Sedang',
                3 => 'Rendah',
            ];
          @endphp
          <span class="badge bg-label-info">
            {{ $prioritas_label[$data_layanan->prioritas] ?? '-' }}
          </span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Status Arsip</label>
        <p class="text-muted mb-0">
          @if ($data_layanan->status_arsip == 1)
            <span class="badge bg-label-danger">Diarsipkan</span>
          @else
            <span class="badge bg-label-success">Aktif</span>
          @endif
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Penanggung Jawab (PIC)</label>
        <p class="text-muted mb-0">
          @if ($data_layanan->penanggungJawab->count() > 0)
            @foreach ($data_layanan->penanggungJawab as $pic)
              <span class="badge bg-label-primary me-1">
                {{ $pic->user->name ?? 'Tanpa Nama' }}
              </span>
            @endforeach
          @else
            <span class="text-muted">Belum ditentukan</span>
          @endif
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Dibuat pada</label>
        <p class="text-muted mb-0">
          {{ $data_layanan->created_at->format('d M Y H:i') }}
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Diperbarui pada</label>
        <p class="text-muted mb-0">
          {{ $data_layanan->updated_at->format('d M Y H:i') }}
        </p>
      </div>
      <a href="{{ route('service.unit', ['slug' => $slug]) }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali
      </a>
    </div>
  </div>
@endsection
