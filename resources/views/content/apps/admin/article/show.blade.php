@extends('layouts/layoutMaster')

@section('title', 'Detail Artikel')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail Artikel</h5>
    <div class="card-body">
      {{-- JUDUL --}}
      <div class="mb-3">
        <label class="form-label">Judul</label>
        <p class="form-control">{{ $data_artikel->judul }}</p>
      </div>
      {{-- KATEGORI --}}
      <div class="mb-3">
        <label class="form-label">Kategori</label>
        <p class="form-control">{{ $data_artikel->kategori->kategori ?? '-' }}</p>
      </div>
      {{-- GAMBAR (PREVIEW) --}}
      <div class="mb-3">
        <label class="form-label">Gambar (Thumbnail)</label><br>
        @if ($data_artikel->gambar)
          <img src="{{ asset('storage/' . $data_artikel->gambar) }}" alt="Gambar Artikel" class="img-fluid rounded"
            style="max-width: 300px; display: block; margin-left: 0;">
        @else
          <p class="form-control">Tidak ada gambar</p>
        @endif
      </div>
      {{-- DESKRIPSI --}}
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <div class="form-control">
          {!! $data_artikel->deskripsi !!}
        </div>
      </div>
      {{-- STATUS --}}
      <div class="mb-3">
        <label class="form-label">Status</label>
        <p class="form-control">{{ $data_artikel->status }}</p>
      </div>
      {{-- PENULIS --}}
      <div class="mb-3">
        <label class="form-label">Penulis</label>
        <p class="form-control">{{ $data_artikel->user->name ?? '-' }}</p>
      </div>
      {{-- DIBUAT PADA --}}
      <div class="mb-3">
        <label class="form-label">Dibuat pada</label>
        <p class="form-control">{{ $data_artikel->created_at->format('d M Y H:i') }}</p>
      </div>
      {{-- DIPERBARUI PADA --}}
      <div class="mb-3">
        <label class="form-label">Diperbarui pada</label>
        <p class="form-control">{{ $data_artikel->updated_at->format('d M Y H:i') }}</p>
      </div>
      {{-- BUTTON KEMBALI --}}
      <a href="{{ route('article.index') }}" class="btn btn-outline-secondary mt-3">Kembali</a>
    </div>
  </div>
@endsection
