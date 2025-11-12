@extends('layouts/layoutMaster')

@section('title', 'Edit Mahasiswa')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Edit Data Mahasiswa</h5>
    <div class="card-body">
      <form action="{{ route('student.update', $data_mahasiswa->id) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- NAMA LENGKAP --}}
        <div class="mb-6">
          <label for="name" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $data_mahasiswa->user->name ?? '') }}" placeholder="Nama Lengkap" required />
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- NIM --}}
        <div class="mb-6">
          <label for="nim" class="form-label">NIM</label>
          <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim" name="nim"
            value="{{ old('nim', $data_mahasiswa->nim) }}" placeholder="Nomor Induk Mahasiswa" required />
          @error('nim')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- EMAIL --}}
        <div class="mb-6">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            value="{{ old('email', $data_mahasiswa->user->email ?? '') }}" placeholder="nim@student.polindra.ac.id"
            required />
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- PROGRAM STUDI --}}
        <div class="mb-6">
          <label for="program_studi" class="form-label">Program Studi</label>
          <select class="form-select @error('program_studi') is-invalid @enderror" id="program_studi" name="program_studi"
            required>
            <option value="" disabled>Pilih Program Studi</option>
            @foreach ($data_program_studi as $jurusan => $prodiGroup)
              <optgroup label="{{ $jurusan }}">
                @foreach ($prodiGroup as $prodi)
                  <option value="{{ $prodi->id }}"
                    {{ old('program_studi', $data_mahasiswa->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                    {{ $prodi->program_studi }}
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          </select>
          @error('program_studi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- TAHUN MASUK --}}
        <div class="mb-6">
          <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
          <input type="number" class="form-control @error('tahun_masuk') is-invalid @enderror" id="tahun_masuk"
            name="tahun_masuk" value="{{ old('tahun_masuk', $data_mahasiswa->tahun_masuk) }}" placeholder="2024"
            min="2000" max="2100" required />
          @error('tahun_masuk')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Perbarui</button>
          <a href="{{ route('student.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
