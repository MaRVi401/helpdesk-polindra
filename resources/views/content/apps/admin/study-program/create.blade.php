@extends('layouts/layoutMaster')

@section('title', 'Tambah Program Studi')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Tambah Jurusan</h5>
    <div class="card-body">
      <form action="{{ route('study-program.store') }}" method="POST">
        @csrf
        {{-- NAMA PROGRAM STUDI --}}
        <div class="mb-6">
          <label for="judul" class="form-label">Nama Program Studi</label>
          <input type="text" class="form-control @error('program_studi') is-invalid @enderror" id="program_studi"
            name="program_studi" placeholder="contoh: D4 - Rekayasa Perangkat Lunak" required value="{{ old('program_studi') }}" />
          @error('program_studi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- NAMA JURUSAN --}}
        <div class="mb-6">
          <label for="kategori_id" class="form-label">Nama Jurusan</label>
          <select class="form-select @error('jurusan_id') is-invalid @enderror" id="jurusan_id" name="jurusan_id"
            required>
            <option value="" disabled selected>Pilih jurusan</option>
            @foreach ($data_jurusan as $jurusan)
              <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                {{ $jurusan->nama_jurusan }}
              </option>
            @endforeach
          </select>
          @error('jurusan_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a href="{{ route('study-program.index') }}"><button class="btn btn-outline-secondary w-full"
              type="button">Batal</button></a>
        </div>
      </form>
    </div>
  </div>
@endsection
