@extends('layouts/layoutMaster')

@section('title', 'Edit Artikel')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Edit Artikel</h5>
    <div class="card-body">
      <form action="{{ route('article.update', $data_artikel->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        {{-- JUDUL --}}
        <div class="mb-4">
          <label for="judul" class="form-label">Judul</label>
          <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror"
            placeholder="Judul Artikel" value="{{ old('judul', $data_artikel->judul) }}" required>
          @error('judul')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- KATEGORI --}}
        <div class="mb-4">
          <label for="kategori_id" class="form-label">Kategori</label>
          <select id="kategori_id" name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror"
            required>
            <option value="" disabled>Pilih kategori</option>
            @foreach ($data_kategori as $kategori)
              <option value="{{ $kategori->id }}"
                {{ old('kategori_id', $data_artikel->kategori_id) == $kategori->id ? 'selected' : '' }}>
                {{ $kategori->kategori }}
              </option>
            @endforeach
          </select>
          @error('kategori_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- DESKRIPSI --}}
        <div class="mb-4">
          <label for="deskripsi" class="form-label">Deskripsi</label>
          <textarea id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
            placeholder="Deskripsi Artikel..." required rows="5">{{ old('deskripsi', $data_artikel->deskripsi) }}</textarea>
          @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- GAMBAR --}}
        <div class="mb-4">
          <label for="gambar" class="form-label">Gambar (Image)</label>
          <input class="form-control @error('gambar') is-invalid @enderror" type="file" id="gambar" name="gambar"
            accept="image/jpeg,image/png,image/jpg">
          <small class="text-muted d-block mt-1">Format: JPEG, PNG, JPG. Maksimal 2MB. (400 x 200)</small>
          {{-- PREVIEW GAMBAR LAMA --}}
          @if ($data_artikel->gambar)
            <div class="mt-3">
              <img src="{{ asset('storage/' . $data_artikel->gambar) }}" alt="Gambar Artikel" class="img-thumbnail rounded"
                width="250">
            </div>
          @endif
          @if ($data_artikel->gambar)
            <div class="mt-2 text-muted bg-slate-800">
              <small>{{ basename($data_artikel->gambar) }}</small>
            </div>
          @endif
          @error('gambar')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- STATUS --}}
        <div class="mb-4">
          <label for="status" class="form-label">Status</label>
          <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="" disabled>Pilih status</option>
            <option value="Draft" {{ old('status', $data_artikel->status) == 'Draft' ? 'selected' : '' }}>Draft</option>
            <option value="Post" {{ old('status', $data_artikel->status) == 'Post' ? 'selected' : '' }}>Post</option>
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mt-4">
          <button class="btn btn-primary" type="submit">Perbarui</button>
          <a href="{{ route('article.index') }}" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
