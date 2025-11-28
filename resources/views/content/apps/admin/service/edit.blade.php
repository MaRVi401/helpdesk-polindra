@extends('layouts/layoutMaster')

@section('title', 'Edit Layanan')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Edit Layanan - {{ $data_layanan->nama }}</h5>
    <div class="card-body">
      <form action="{{ route('service.update', $data_layanan->id) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- NAMA LAYANAN --}}
        <div class="mb-6">
          <label class="form-label">Nama Layanan</label>
          <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
            value="{{ old('nama', $data_layanan->nama) }}" required>
          @error('nama')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- UNIT --}}
        <div class="mb-6">
          <label class="form-label">Unit</label>
          <select class="form-select @error('unit_id') is-invalid @enderror" name="unit_id" required>
            <option value="" disabled>Pilih Unit</option>
            @foreach ($data_unit as $unit)
              <option value="{{ $unit->id }}"
                {{ old('unit_id', $data_layanan->unit_id) == $unit->id ? 'selected' : '' }}>
                {{ $unit->nama_unit }}
              </option>
            @endforeach
          </select>
          @error('unit_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- PRIORITAS --}}
        <div class="mb-6">
          <label class="form-label">Prioritas</label>
          <select class="form-select @error('prioritas') is-invalid @enderror" name="prioritas" required>
            <option value="1" {{ $data_layanan->prioritas == 1 ? 'selected' : '' }}>Rendah</option>
            <option value="2" {{ $data_layanan->prioritas == 2 ? 'selected' : '' }}>Sedang</option>
            <option value="3" {{ $data_layanan->prioritas == 3 ? 'selected' : '' }}>Tinggi</option>
          </select>
          @error('prioritas')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- STATUS ARSIP --}}
        <div class="mb-6">
          <label class="form-label">Status Arsip</label>
          <select class="form-select @error('status_arsip') is-invalid @enderror" name="status_arsip" required>
            <option value="0" {{ $data_layanan->status_arsip == 0 ? 'selected' : '' }}>Aktif</option>
            <option value="1" {{ $data_layanan->status_arsip == 1 ? 'selected' : '' }}>Diarsipkan</option>
          </select>
          @error('status_arsip')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- PENANGGUNG JAWAB --}}
        <div class="mb-6">
          <label class="form-label">Penanggung Jawab (PIC)</label>
          <select class="form-select @error('penanggung_jawab_ids') is-invalid @enderror" name="penanggung_jawab_ids[]">
            <option value="" disabled
              {{ empty(old('penanggung_jawab_ids', $data_layanan->penanggungJawab->pluck('id')->toArray())) ? 'selected' : '' }}>
              Belum ditentukan
            </option>
            @foreach ($data_staf as $staf)
              <option value="{{ $staf->id }}"
                {{ in_array($staf->id, old('penanggung_jawab_ids', $data_layanan->penanggungJawab->pluck('id')->toArray())) ? 'selected' : '' }}>
                {{ $staf->user->name }}
              </option>
            @endforeach
          </select>
          @error('penanggung_jawab_ids')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Perbarui</button>
          <a href="{{ route('service.unit', ['slug' => $slug]) }}" class="btn btn-outline-secondary">
            Batal
          </a>
        </div>
      </form>
    </div>
  </div>
@endsection
