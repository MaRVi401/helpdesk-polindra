@extends('layouts.layoutMaster')

@section('title', 'Edit Jabatan')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Jabatan: {{ $data_position->nama_jabatan }}</h5>
            </div>
            <div class="card-body">
                {{-- Form mengarah ke route update dengan method PUT/PATCH --}}
                <form action="{{ route('position.update', $data_position->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama_jabatan" class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                        {{-- Menggunakan old() dengan fallback untuk mempertahankan nilai jika validasi gagal --}}
                        <input type="text" class="form-control @error('nama_jabatan') is-invalid @enderror"
                            id="nama_jabatan" name="nama_jabatan" placeholder="Masukkan nama jabatan"
                            value="{{ old('nama_jabatan', $data_position->nama_jabatan) }}" required>
                        @error('nama_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between gap-2">
                        <a href="{{ route('position.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
