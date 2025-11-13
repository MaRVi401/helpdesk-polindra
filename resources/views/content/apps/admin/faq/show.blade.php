@extends('layouts/layoutMaster')

@section('title', 'Detail FAQ')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail FAQ</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label fw-semibold">Judul</label>
        <p class="text-muted mb-0">{{ $data_faq->judul }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Layanan</label>
        <p class="text-muted mb-0">
          {{ $data_faq->layanan->nama ?? '-' }}
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <p class="text-muted mb-0">
          {{ $data_faq->deskripsi }}
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Status</label>
        <p class="text-muted mb-0">
          <span class="badge 
          {{ $data_faq->status === 'Post' ? 'bg-label-success' : 'bg-label-warning' }}">
            {{ ucfirst($data_faq->status) }}
          </span>
        </p>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Dibuat oleh</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">{{ $data_faq->user->name ?? '-' }}</span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Dibuat pada</label>
        <p class="text-muted mb-0">{{ $data_faq->created_at->format('d M Y H:i') }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Diperbarui pada</label>
        <p class="text-muted mb-0">{{ $data_faq->updated_at->format('d M Y H:i') }}</p>
      </div>
      <a href="{{ route('faq.index') }}" class="btn btn-outline-secondary mt-3">
        <i class="icon-base ti tabler-arrow-left me-1"></i>
        Kembali
      </a>
    </div>
  </div>
@endsection
