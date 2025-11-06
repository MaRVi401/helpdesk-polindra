@extends('layouts/layoutMaster')

@section('title', 'Tambah Kategori')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Tambah Kategori Artikel</h5>
    <div class="card-body">
      <form action="{{ route('article-category.store') }}" method="POST">
        @csrf
        {{-- KATEGORI --}}
        <div class="mb-6">
          <label for="kategori" class="form-label">Kategori</label>
          <input type="text" class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori"
            placeholder="Nama Kategori" required value="{{ old('kategori') }}" />
          @error('kategori')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a href="{{ route('faq.index') }}"><button class="btn btn-secondary w-full" type="button">Batal</button></a>
        </div>
      </form>
    </div>
  </div>
@endsection
