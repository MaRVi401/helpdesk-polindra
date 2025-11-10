@extends('layouts/layoutMaster')

@section('title', 'Detail FAQ')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail FAQ</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Judul</label>
        <p class="form-control">{{ $data_faq->judul }}</p>
      </div>

      <div class="mb-3">
        <label class="form-label">Layanan</label>
        <p class="form-control">{{ $data_faq->layanan->nama ?? '-' }}</p>
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <p class="form-control">{{ $data_faq->deskripsi }}</p>
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <p class="form-control">{{ $data_faq->status }}</p>
      </div>

      <div class="mb-3">
        <label class="form-label">Dibuat oleh</label>
        <p class="form-control">{{ $data_faq->user->name ?? '-' }}</p>
      </div>

      <div class="mb-3">
        <label class="form-label">Dibuat pada</label>
        <p class="form-control">{{ $data_faq->created_at->format('d M Y H:i') }}</p>
      </div>

      <div class="mb-3">
        <label class="form-label">Diperbarui pada</label>
        <p class="form-control">{{ $data_faq->updated_at->format('d M Y H:i') }}</p>
      </div>

      <a href="{{ route('faq.index') }}" class="btn btn-outline-secondary mt-3">Kembali</a>
    </div>
  </div>
@endsection
