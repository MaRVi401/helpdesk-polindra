@extends('layouts/layoutMaster')

@section('title', 'Edit Jurusan')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Edit Jurusan</h5>
    <div class="card-body">
      <form action="{{ route('major.update', $data_jurusan->id) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- NAMA JURUSAN --}}
        <div class="mb-6">
          <label for="judul" class="form-label">Nama Jurusan</label>
          <input type="text" class="form-control @error('nama_jurusan') is-invalid @enderror" id="nama_jurusan"
            name="nama_jurusan" placeholder="Nama Jurusan" required value="{{ old('nama_jurusan', $data_jurusan->nama_jurusan) }}" />
          @error('nama_jurusan')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a href="{{ route('major.index') }}"><button class="btn btn-outline-secondary w-full"
              type="button">Batal</button></a>
        </div>
      </form>
    </div>
  </div>
@endsection
