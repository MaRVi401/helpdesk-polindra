@extends('layouts/layoutMaster')

@section('title', 'Edit Staf')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Edit Data Staf</h5>
    <div class="card-body">
      <form action="{{ route('staff.update', $data_staf->id) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- NAMA LENGKAP --}}
        <div class="mb-6">
          <label for="name" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $data_staf->user->name ?? '') }}" placeholder="Nama Lengkap" required />
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- NIK --}}
        <div class="mb-6">
          <label for="nik" class="form-label">NIK</label>
          <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik"
            maxlength="16" value="{{ old('nik', $data_staf->nik) }}" placeholder="Nomor Induk Karyawan" required />
          @error('nik')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- EMAIL --}}
        <div class="mb-6">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            value="{{ old('email', $data_staf->user->email ?? '') }}" placeholder="email@polindra.ac.id" required />
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- ROLE --}}
        <div class="mb-6">
          <label for="role" class="form-label">Role</label>
          @php
            $currentRole = $data_staf->user->role ?? '';
          @endphp
          <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
            {{ $currentRole === 'super_admin' ? 'disabled' : '' }} required>
            <option value="" disabled>Pilih Role</option>
            {{-- Hanya tampilkan opsi jika bukan super_admin --}}
            @if ($currentRole !== 'super_admin')
              <option value="admin_unit" {{ old('role', $currentRole) == 'admin_unit' ? 'selected' : '' }}>
                Admin Unit
              </option>
              <option value="kepala_unit" {{ old('role', $currentRole) == 'kepala_unit' ? 'selected' : '' }}>
                Kepala Unit
              </option>
            @else
              {{-- Jika super_admin, tampilkan sebagai teks tetap --}}
              <option selected>Super Admin</option>
            @endif
          </select>
          {{-- Hidden input agar value tetap terkirim ke server meski select disabled --}}
          @if ($currentRole === 'super_admin')
            <input type="hidden" name="role" value="super_admin">
          @endif
          @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- UNIT --}}
        <div class="mb-6">
          <label for="unit_id" class="form-label">Unit</label>
          <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
            <option value="" disabled>Pilih Unit</option>
            @foreach ($data_unit as $unit)
              <option value="{{ $unit->id }}"
                {{ old('unit_id', $data_staf->unit_id) == $unit->id ? 'selected' : '' }}>
                {{ $unit->nama_unit }}
              </option>
            @endforeach
          </select>
          @error('unit_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- JABATAN --}}
        <div class="mb-6">
          <label for="jabatan_id" class="form-label">Jabatan</label>
          @php
            $currentRole = $data_staf->user->role ?? '';
            $currentJabatanId = old('jabatan_id', $data_staf->jabatan_id);
          @endphp
          <select class="form-select @error('jabatan_id') is-invalid @enderror" id="jabatan_id" name="jabatan_id"
            {{ $currentRole === 'super_admin' ? 'disabled' : '' }} required>
            <option value="" disabled>Pilih Jabatan</option>
            @foreach ($data_jabatan as $jabatan)
              @if ($jabatan->nama_jabatan === 'Super Administrator')
                {{-- Hanya tampil jika role super_admin --}}
                @if ($currentRole === 'super_admin')
                  <option value="{{ $jabatan->id }}" {{ $currentJabatanId == $jabatan->id ? 'selected' : '' }}>
                    {{ $jabatan->nama_jabatan }}
                  </option>
                @endif
              @else
                {{-- Selain Super Administrator, tampil untuk semua --}}
                @if ($currentRole !== 'super_admin')
                  <option value="{{ $jabatan->id }}" {{ $currentJabatanId == $jabatan->id ? 'selected' : '' }}>
                    {{ $jabatan->nama_jabatan }}
                  </option>
                @endif
              @endif
            @endforeach
          </select>

          {{-- Hidden input agar value tetap dikirim saat field disabled --}}
          @if ($currentRole === 'super_admin')
            <input type="hidden" name="jabatan_id" value="{{ $currentJabatanId }}">
          @endif

          @error('jabatan_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Perbarui</button>
          <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
