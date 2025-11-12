@extends('layouts/layoutMaster')

@section('title', 'Detail Mahasiswa')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail Data Mahasiswa</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <p class="form-control">{{ $data_mahasiswa->user->name ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">NIM</label>
        <p class="form-control">{{ $data_mahasiswa->nim }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <p class="form-control">{{ $data_mahasiswa->user->email ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Program Studi</label>
        <p class="form-control">{{ $data_mahasiswa->programStudi->program_studi ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Jurusan</label>
        <p class="form-control">{{ $data_mahasiswa->programStudi->jurusan->nama_jurusan ?? '-' }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Tahun Masuk</label>
        <p class="form-control">{{ $data_mahasiswa->tahun_masuk }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Data akun dibuat</label>
        <p class="form-control">{{ $data_mahasiswa->created_at->format('d M Y H:i') }}</p>
      </div>
      <div class="mb-3">
        <label class="form-label">Data akun diperbarui</label>
        <p class="form-control">{{ $data_mahasiswa->updated_at->format('d M Y H:i') }}</p>
      </div>
      <a href="{{ route('student.index') }}" class="btn btn-outline-secondary mt-3">Kembali</a>
    </div>
  </div>
@endsection
