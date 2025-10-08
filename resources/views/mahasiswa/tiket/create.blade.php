@extends('layouts.app')
@section('title', 'Buat Tiket Layanan Baru')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Layanan /</span> Buat Tiket Baru</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Formulir Pengajuan Tiket Layanan</h5>
                <div class="card-body">
                    <form action="{{ route('mahasiswa.tiket.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="layanan_id" class="form-label">Jenis Layanan</label>
                            <select class="form-select @error('layanan_id') is-invalid @enderror" id="layanan_id" name="layanan_id" required>
                                <option value="" disabled selected>-- Pilih Jenis Layanan --</option>
                                @foreach($layanans as $layanan)
                                    <option value="{{ $layanan->id }}" {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>{{ $layanan->nama }}</option>
                                @endforeach
                            </select>
                            @error('layanan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi / Keterangan</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan kebutuhan atau masalah Anda..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Ajukan Tiket</button>
                            <a href="{{ route('mahasiswa.tiket.index') }}" class="btn btn-label-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection