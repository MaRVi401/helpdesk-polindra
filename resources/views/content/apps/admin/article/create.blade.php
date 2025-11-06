@extends('layouts/layoutMaster')

@section('title', 'Tambah Artikel')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Tambah Artikel</h5>
    <div class="card-body">
      <form action="{{ route('article.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- JUDUL --}}
        <div class="mb-6">
          <label for="judul" class="form-label">Judul</label>
          <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul"
            placeholder="Judul Artikel" value="{{ old('judul') }}" required />
          @error('judul')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- KATEGORI --}}
        <div class="mb-6">
          <label for="kategori_id" class="form-label">Kategori</label>
          <select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id"
            required>
            <option value="" disabled selected>Pilih kategori</option>
            @foreach ($data_kategori as $kategori)
              <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                {{ $kategori->kategori }}
              </option>
            @endforeach
          </select>
          @error('kategori_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- DESKRIPSI --}}
        <div class="mb-6">
          <label for="deskripsi" class="form-label">Deskripsi</label>
          <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi"
            placeholder="Deskripsi Artikel..." required rows="3">{{ old('deskripsi') }}</textarea>
          @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- GAMBAR --}}
        <div class="mb-6">
          <label for="gambar" class="form-label">Gambar (Image)</label>
          <input class="form-control @error('gambar') is-invalid @enderror" type="file" id="gambar" name="gambar"
            accept="image/jpeg,image/png,image/jpg,image/gif" />
          <small class="text-muted">Format: JPEG, PNG, JPG. Maksimal 2MB. (400 x 200)</small>
          @error('gambar')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- STATUS --}}
        <div class="mb-6">
          <label for="status" class="form-label">Status</label>
          <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="" disabled selected>Pilih status</option>
            <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
            <option value="Post" {{ old('status') == 'Post' ? 'selected' : '' }}>Post</option>
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a href="{{ route('article.index') }}" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
