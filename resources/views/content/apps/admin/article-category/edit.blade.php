@extends('layouts/layoutMaster')

@section('title', 'Edit Kategori Artikel')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Edit Kategori Artikel</h5>
    <div class="card-body">
      <form action="{{ route('article-category.update', $data_kategori->id) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- KATEGORI --}}
        <div class="mb-6">
          <label for="kategori" class="form-label">Kategori</label>
          <input type="text" class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori"
            value="{{ old('kategori', $data_kategori->kategori) }}" placeholder="Nama kategori" required />
          @error('kategori')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Perbarui</button>
          <a href="{{ route('article-category.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
