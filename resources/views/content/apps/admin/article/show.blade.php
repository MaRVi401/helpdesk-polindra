@extends('layouts/layoutMaster')

@section('title', 'Detail Artikel')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail Artikel</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label fw-semibold">Judul</label>
        <p class="text-muted mb-0">{{ $data_artikel->judul }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Kategori</label>
        <p class="text-muted mb-0">{{ $data_artikel->kategori->kategori ?? '-' }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Gambar (Thumbnail)</label>
        @if ($data_artikel->gambar)
          <img src="{{ asset('storage/' . $data_artikel->gambar) }}" alt="Gambar Artikel" class="img-fluid rounded"
            style="max-width: 300px; display: block; margin-left: 0;">
        @else
          <p class="text-muted mb-0">Tidak ada gambar</p>
        @endif
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <p class="text-muted mb-0">{!! $data_artikel->deskripsi !!}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Status</label>
        <p class="text-muted mb-0">
          <span class="badge 
          {{ $data_artikel->status === 'Post' ? 'bg-label-success' : 'bg-label-warning' }}">
            {{ ucfirst($data_artikel->status) }}
          </span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Penulis</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">{{ $data_artikel->user->name ?? '-' }}</span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Dibuat pada</label>
        <p class="text-muted mb-0">{{ $data_artikel->created_at->format('d M Y H:i') }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Diperbarui pada</label>
        <p class="text-muted mb-0">{{ $data_artikel->updated_at->format('d M Y H:i') }}</p>
      </div>
      <a href="{{ route('article.index') }}" class="btn btn-outline-secondary mt-3">
        <i class="icon-base ti tabler-arrow-left me-1"></i>
        Kembali
      </a>
    </div>
  </div>
@endsection
