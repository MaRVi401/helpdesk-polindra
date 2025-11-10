@extends('layouts/layoutMaster')

@section('title', 'Tambah FAQ')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Tambah Frequently Asked Questions (FAQ)</h5>
    <div class="card-body">
      <form action="{{ route('faq.store') }}" method="POST">
        @csrf
        {{-- JUDUL --}}
        <div class="mb-6">
          <label for="judul" class="form-label">Judul</label>
          <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul"
            placeholder="Judul FAQ" required value="{{ old('judul') }}" />
          @error('judul')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- LAYANAN --}}
        <div class="mb-6">
          <label for="layanan_id" class="form-label">Layanan</label>
          <select class="form-select @error('layanan_id') is-invalid @enderror" id="layanan_id" name="layanan_id"
            required>
            <option value="" disabled selected>Pilih layanan</option>
            @foreach ($data_layanan as $layanan)
              <option value="{{ $layanan->id }}" {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>
                {{ $layanan->nama }}</option>
            @endforeach
          </select>
          @error('layanan_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- DESKRIPSI --}}
        <div class="mb-6">
          <label for="deskripsi" class="form-label">Deskripsi</label>
          <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi"
            placeholder="Deskripsi FAQ..." required rows="3">{{ old('deskripsi') }}</textarea>
          @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- STATUS --}}
        <div class="mb-6">
          <label for="status" class="form-label">Status</label>
          <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required
            required>
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
          <a href="{{ route('faq.index') }}"><button class="btn btn-outline-secondary w-full"
              type="button">Batal</button></a>
        </div>
      </form>
    </div>
  </div>
@endsection
