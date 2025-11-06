@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Layanan')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Admin Unit / Kelola Layanan /</span> Edit Layanan
</h4>

<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <h5 class="card-header">Formulir Edit Layanan</h5>
      <div class="card-body">
        <form action="{{ route('admin_unit.layanan.update', $layanan->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label for="id_unit" class="form-label">Unit</label>
            <select class="form-select @error('id_unit') is-invalid @enderror" id="id_unit" name="id_unit" required>
              <option value="">-- Pilih Unit --</option>
              @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ old('id_unit', $layanan->id_unit) == $unit->id ? 'selected' : '' }}>
                  {{ $unit->nama_unit }}
                </option>
              @endforeach
            </select>
            @error('id_unit')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="nama_layanan" class="form-label">Nama Layanan</label>
            <input type="text" class="form-control @error('nama_layanan') is-invalid @enderror" id="nama_layanan" name="nama_layanan" value="{{ old('nama_layanan', $layanan->nama_layanan) }}" required>
            @error('nama_layanan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
            @error('deskripsi')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
              <option value="Aktif" {{ old('status', $layanan->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
              <option value="Tidak Aktif" {{ old('status', $layanan->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="btn btn-primary">Update</button>
          <a href="{{ route('admin_unit.layanan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection