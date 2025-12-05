@extends('layouts.layoutMaster')

@section('title', 'Tambah Jabatan')

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Tambah Jabatan</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('position.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="nama_jabatan" class="form-label">Nama Jabatan</label>
            {{-- NAMA JABATAN --}}
            <input type="text" class="form-control @error('nama_jabatan') is-invalid @enderror" id="nama_jabatan"
              name="nama_jabatan" placeholder="Masukkan nama jabatan (e.g., Dosen, Staff IT)"
              value="{{ old('nama_jabatan') }}" required>
            @error('nama_jabatan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          {{-- BUTTON --}}
          <div class="mb-6">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a href="{{ route('position.index') }}"><button class="btn btn-outline-secondary w-full"
                type="button">Batal</button></a>
          </div>
      </div>
      </form>
    </div>
  </div>
  </div>
@endsection
