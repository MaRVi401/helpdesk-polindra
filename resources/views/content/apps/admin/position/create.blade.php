@extends('layouts.layoutMaster')

@section('title', 'Tambah Jabatan')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tambah Jabatan</h5>
            </div>
            <div class="card-body">
                {{-- Form mengarah ke route store dengan method POST --}}
                <form action="{{ route('position.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_jabatan" class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_jabatan') is-invalid @enderror"
                            id="nama_jabatan" name="nama_jabatan"
                            placeholder="Masukkan nama jabatan (e.g., Dosen, Staff IT)" value="{{ old('nama_jabatan') }}"
                            required>
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

            </div>
            </form>
        </div>
    </div>
    </div>
@endsection
