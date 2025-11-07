@extends('layouts/layoutMaster')

@section('title', 'Edit Artikel')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/highlight/highlight.scss', 'resources/assets/vendor/libs/quill/katex.scss', 'resources/assets/vendor/libs/quill/editor.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/highlight/highlight.js', 'resources/assets/vendor/libs/quill/quill.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/form-basic-inputs.js', 'resources/assets/js/forms-editors.js'])
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
        <div class="mb-6">
          <label for="deskripsi" class="form-label">Deskripsi</label>
          {{-- Editor area --}}
          <div id="snow-editor" class="@error('deskripsi') is-invalid @enderror">
            {!! old('deskripsi', $data_artikel->deskripsi) !!}
          </div>
          {{-- Hidden input untuk menyimpan konten --}}
          <input type="hidden" name="deskripsi" id="deskripsi" value="{{ old('deskripsi', $data_artikel->deskripsi) }}"
            required>
          @error('deskripsi')
            <div class="invalid-feedback d-block">{{ $message }}</div>
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
              <img src="{{ asset('storage/' . $data_artikel->gambar) }}" alt="Gambar Artikel"
                class="img-thumbnail rounded" width="250">
            </div>
          @endif
          @if ($data_artikel->gambar)
            <div class="mt-2 text-muted">
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
