@extends('layouts/layoutMaster')

@section('title', 'Detail Jurusan')

@section('content')
  <div class="row">
    <div class="col-md-5">
      <div class="card mb-4">
        <h5 class="card-header">Detail Jurusan</h5>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Jurusan</label>
            <p class="text-muted mb-0">{{ $data_jurusan->nama_jurusan }}</p>
          </div>
          <hr>
          <div class="mb-3">
            <label class="form-label fw-semibold">Jumlah Program Studi</label>
            <p class="text-muted mb-0">
              <span class="badge bg-label-primary">{{ $data_jurusan->programStudi->count() }} Program Studi</span>
            </p>
          </div>
          <hr>
          <div class="mb-3">
            <label class="form-label fw-semibold">Dibuat pada</label>
            <p class="text-muted mb-0">{{ $data_jurusan->created_at->format('d M Y H:i') }}</p>
          </div>
          <hr>
          <div class="mb-3">
            <label class="form-label fw-semibold">Diperbarui pada</label>
            <p class="text-muted mb-0">{{ $data_jurusan->updated_at->format('d M Y H:i') }}</p>
          </div>
          <a href="{{ route('major.index') }}" class="btn btn-outline-secondary mt-3">
            <i class="icon-base ti tabler-arrow-left me-1"></i>
            Kembali
          </a>
        </div>
      </div>
    </div>
    {{-- DAFTAR PROGRAM STUDI --}}
    <div class="col-md-7">
      <div class="card">
        <h5 class="card-header">Daftar Program Studi</h5>
        <div class="card-body">
          @if ($data_jurusan->programStudi->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Program Studi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($data_jurusan->programStudi as $index => $prodi)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>{{ $prodi->program_studi }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-5">
              <i class="icon-base ti tabler-folder-open icon-lg text-muted mb-3" style="font-size: 3rem;"></i>
              <p class="text-muted">Belum ada program studi untuk jurusan ini</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
