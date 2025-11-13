@extends('layouts/layoutMaster')

@section('title', 'Detail Mahasiswa')

@section('content')
  <div class="card">
    <h5 class="card-header">Detail Data Mahasiswa</h5>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Lengkap</label>
        <p class="text-muted mb-0">{{ $data_mahasiswa->user->name ?? '-' }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">NIM (Nomor Induk Mahasiswa)</label>
        <p class="text-muted mb-0">{{ $data_mahasiswa->nim }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <p class="text-muted mb-0">{{ $data_mahasiswa->user->email ?? '-' }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Program Studi</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">{{ $data_mahasiswa->programStudi->program_studi ?? '-' }}</span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Jurusan</label>
        <p class="text-muted mb-0">
          <span class="badge bg-label-primary">{{ $data_mahasiswa->programStudi->jurusan->nama_jurusan ?? '-' }}</span>
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Tahun Masuk</label>
        <p class="text-muted mb-0">
          {{ $data_mahasiswa->tahun_masuk }}
        </p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Data akun dibuat</label>
        <p class="text-muted mb-0">{{ $data_mahasiswa->created_at->format('d M Y H:i') }}</p>
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label fw-semibold">Data akun diperbarui</label>
        <p class="text-muted mb-0">{{ $data_mahasiswa->updated_at->format('d M Y H:i') }}</p>
      </div>
      <a href="{{ route('student.index') }}" class="btn btn-outline-secondary mt-3">
        <i class="icon-base ti tabler-arrow-left me-1"></i>
        Kembali
      </a>
    </div>
  </div>
@endsection
