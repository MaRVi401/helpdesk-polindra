@extends('layouts.contentNavbarLayout')

@section('title', 'Buat Tiket Baru (Admin)')

@section('content')
<h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Tiket /</span> Buat Baru
</h4>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Form Pembuatan Tiket Baru</h5>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        {{-- Menggunakan route name yang baru --}}
        <form method="POST" action="{{ route('ticket.store') }}">
            @csrf
            
            <div class="mb-3">
                <label for="pemohon_id" class="form-label">Pemohon (Mahasiswa)</label>
                <select class="form-select @error('pemohon_id') is-invalid @enderror" id="pemohon_id" name="pemohon_id" required>
                    <option value="">Pilih Pemohon</option>
                    @foreach($mahasiswas as $mahasiswa)
                        <option value="{{ $mahasiswa->id }}" {{ old('pemohon_id') == $mahasiswa->id ? 'selected' : '' }}>
                            {{ $mahasiswa->name }} ({{ $mahasiswa->mahasiswa->nim ?? 'NIM tidak tersedia' }})
                        </option>
                    @endforeach
                </select>
                @error('pemohon_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="layanan_id" class="form-label">Layanan</label>
                <select class="form-select @error('layanan_id') is-invalid @enderror" id="layanan_id" name="layanan_id" required>
                    <option value="">Pilih Layanan</option>
                    @foreach($layanans as $layanan)
                        <option value="{{ $layanan->id }}" {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>
                            {{ $layanan->nama }} (Unit: {{ $layanan->unit->nama_unit ?? 'Tidak ada' }})
                        </option>
                    @endforeach
                </select>
                @error('layanan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Permintaan</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Buat Tiket</button>
            {{-- Menggunakan route name yang baru --}}
            <a href="{{ route('ticket.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection