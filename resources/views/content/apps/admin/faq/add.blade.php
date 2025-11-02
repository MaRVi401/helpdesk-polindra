@extends('layouts/layoutMaster')

@section('title', 'Tambah FAQ')

@section('page-script')
  @vite('resources/assets/js/form-basic-inputs.js')
@endsection

@section('content')
  <div class="card">
    <h5 class="card-header">Tambah Frequently Asked Questions (FAQ)</h5>
    <div class="card-body">
      <form action="{{ route('faq.store') }}" method="POST">
        @csrf
        {{-- JUDUL --}}
        <div class="mb-6">
          <label for="judul" class="form-label">Judul</label>
          <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul FAQ" required
            aria-describedby="defaultFormControlHelp" />
        </div>
        {{-- LAYANAN --}}
        <div class="mb-6">
          <label for="layanan_id" class="form-label">Layanan</label>
          <select class="form-select" id="layanan_id" name="layanan_id" required aria-label="Default select example">
            <option value="" disabled selected>Pilih layanan</option>
            @foreach ($data_layanan as $layanan)
              <option value="{{ $layanan->id }}">
                {{ $layanan->nama }}</option>
            @endforeach
          </select>
        </div>
        {{-- DESKRIPSI --}}
        <div class="mb-6"> 
          <label for="deskripsi" class="form-label">Deskripsi</label>
          <textarea class="form-control" id="deskripsi" name="deskripsi" placeholder="Deskripsi FAQ..." required rows="3"></textarea>
        </div>
        {{-- STATUS --}}
        <div class="mb-6">
          <label for="status" class="form-label">Status</label>
          <select class="form-select" id="status" name="status" required aria-label="Default select example" required>
            <option value="" disabled selected>Pilih status</option>
            <option value="Draft">Draft</option>
            <option value="Post">Post</option>
          </select>

        </div>
        {{-- BUTTON --}}
        <div class="mb-6">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a href="{{ route('faq.index') }}"><button class="btn btn-secondary w-full" type="button">Batal</button></a>
        </div>
      </form>
    </div>
  </div>
@endsection
