@extends('layouts/layoutMaster')

@section('title', 'Edit Unit')

@section('page-script')
    @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Edit Unit</h5>
        <div class="card-body">
            <form action="{{ route('unit.update', $data_unit) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- NAMA UNIT --}}
                <div class="mb-6">
                    <label for="nama_unit" class="form-label">Nama Unit</label>
                    <input type="text" class="form-control @error('nama_unit') is-invalid @enderror" id="nama_unit"
                        name="nama_unit" placeholder="Nama Unit" required
                        value="{{ old('nama_unit', $data_unit->nama_unit) }}" />
                    @error('nama_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- FIELD SLUG --}}
                <div class="mb-6">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                        name="slug" placeholder="slug-unit-test" value="{{ old('slug', $data_unit->slug) }}" />
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Kosongkan untuk membuat slug otomatis dari Nama Unit.</small>
                </div>
                {{-- KEPALA UNIT --}}
                <div class="mb-6">
                    <label for="kepala_id" class="form-label">Kepala Unit</label>
                    <select class="form-select @error('kepala_id') is-invalid @enderror" id="kepala_id" name="kepala_id">
                        <option value="">Pilih staf</option>
                        @foreach ($data_staf as $staf)
                            <option value="{{ $staf->id }}"
                                {{ old('kepala_id', $data_unit->kepala_id) == $staf->id ? 'selected' : '' }}>
                                {{ $staf->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('kepala_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- BUTTON --}}
                <div class="mb-6">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('unit.index') }}"><button class="btn btn-outline-secondary w-full"
                            type="button">Batal</button></a>
                </div>
            </form>
        </div>
    </div>
@endsection
