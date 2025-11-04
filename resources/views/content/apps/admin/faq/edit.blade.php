@extends('layouts/layoutMaster')

@section('title', 'Edit FAQ')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Edit Frequently Asked Questions (FAQ)</h5>
    <div class="card-body">
      <form action="{{ route('faq.update', $data_faq->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- JUDUL --}}
        <div class="mb-6">
          <label for="judul" class="form-label">Judul</label>
          <input type="text" class="form-control" id="judul" name="judul"
            value="{{ old('judul', $data_faq->judul) }}" placeholder="Judul FAQ" required />
        </div>

        {{-- LAYANAN --}}
        <div class="mb-6">
          <label for="layanan_id" class="form-label">Layanan</label>
          <select class="form-select" id="layanan_id" name="layanan_id" required>
            <option value="" disabled>Pilih layanan</option>
            @foreach ($data_layanan as $layanan)
              <option value="{{ $layanan->id }}" {{ $layanan->id == $data_faq->layanan_id ? 'selected' : '' }}>
                {{ $layanan->nama }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- DESKRIPSI --}}
        <div class="mb-6">
          <label for="deskripsi" class="form-label">Deskripsi</label>
          <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $data_faq->deskripsi) }}</textarea>
        </div>

        {{-- STATUS --}}
        <div class="mb-6">
          <label for="status" class="form-label">Status</label>
          <select class="form-select" id="status" name="status" required>
            <option value="Draft" {{ $data_faq->status == 'Draft' ? 'selected' : '' }}>Draft</option>
            <option value="Post" {{ $data_faq->status == 'Post' ? 'selected' : '' }}>Post</option>
          </select>
        </div>

        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Perbarui</button>
          <a href="{{ route('faq.index') }}" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
