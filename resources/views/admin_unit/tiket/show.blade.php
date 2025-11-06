@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Tiket')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Admin Unit /</span> Detail Tiket
</h4>

<div class="row">

  <div class="col-md-8 col-lg-9">
    
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tiket #{{ $tiket->no_tiket }} - {{ $tiket->judul }}</h5>
        <a href="{{ route('admin_unit.dashboard') }}" class="btn btn-sm btn-secondary">Kembali ke Dashboard</a>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p class="mb-2"><strong>Pemohon:</strong> {{ $tiket->mahasiswa?->user?->nama ?? 'N/A' }}</p>
            <p class="mb-2"><strong>NIM:</strong> {{ $tiket->mahasiswa?->nim ?? 'N/A' }}</p>
            <p class="mb-2"><strong>Layanan:</strong> {{ $tiket->layanan?->nama_layanan ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-2"><strong>Status:</strong> 
              @if ($tiket->status == 'Dibuka')
                <span class="badge bg-label-info">{{ $tiket->status }}</span>
              @elseif ($tiket->status == 'Sedang Dikerjakan')
                <span class="badge bg-label-warning">{{ $tiket->status }}</span>
              @elseif ($tiket->status == 'Ditutup')
                <span class="badge bg-label-danger">{{ $tiket->status }}</span>
              @elseif ($tiket->status == 'Selesai')
                <span class="badge bg-label-success">{{ $tiket->status }}</span>
              @endif
            </p>
            <p class="mb-2"><strong>Prioritas:</strong>
              @if ($tiket->prioritas == 'Rendah')
                <span class="badge bg-label-secondary">{{ $tiket->prioritas }}</span>
              @elseif ($tiket->prioritas == 'Sedang')
                <span class="badge bg-label-warning">{{ $tiket->prioritas }}</span>
              @elseif ($tiket->prioritas == 'Tinggi')
                <span class="badge bg-label-danger">{{ $tiket->prioritas }}</span>
              @endif
            </p>
            <p class="mb-2"><strong>Dibuat:</strong> {{ $tiket->created_at->format('d/m/Y H:i') }}</p>
          </div>
        </div>

        <hr class="my-3">
        
        <h5>Detail Permintaan</h5>
        @if ($tiket->detail)
          @switch($tiket->detail_type)
              @case('App\Models\DetailTiketSuratKetAktif')
                  <p><strong>Keperluan:</strong> {{ $tiket->detail->keperluan }}</p>
                  @break
              @case('App\Models\DetailTiketResetAkun')
                  <p><strong>Nama Akun:</strong> {{ $tiket->detail->nama_akun }}</p>
                  <p><strong>Permasalahan:</strong> {{ $tiket->detail->permasalahan }}</p>
                  @break
              @case('App\Models\DetailTiketUbahDataMhs')
                  <p><strong>Data Lama:</strong> {{ $tiket->detail->data_lama }}</p>
                  <p><strong>Data Baru:</strong> {{ $tiket->detail->data_baru }}</p>
                  <p><strong>Alasan Perubahan:</strong> {{ $tiket->detail->alasan_perubahan }}</p>
                  @break
              @case('App\Models\DetailTiketReqPublikasi')
                  <p><strong>Judul Publikasi:</strong> {{ $tiket->detail->judul_publikasi }}</p>
                  <p><strong>Deskripsi:</strong> {{ $tiket->detail->deskripsi_publikasi }}</p>
                  <p><strong>Link:</strong> <a href="{{ $tiket->detail->link_publikasi }}" target="_blank">{{ $tiket->detail->link_publikasi }}</a></p>
                  @break
              @default
                  <p>Tidak ada detail spesifik untuk layanan ini.</p>
          @endswitch
        @else
          <p>Detail permintaan tidak ditemukan.</p>
        @endif

        @if($tiket->lampiran)
          <hr class="my-3">
          <h5>Lampiran</h5>
          <a href="{{ Storage::url($tiket->lampiran) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Lampiran</a>
        @endif
      </div>
    </div>

    <div class="card mb-4">
      <h5 class="card-header">Histori Tiket</h5>
      <div class="card-body">
        <ul class="list-unstyled">
          @forelse ($tiket->komentar->sortBy('created_at') as $komentar)
            <li class="mb-3">
              <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                  <img src="{{ asset('assets/img/avatars/1.png') }}" alt="User" class="rounded-circle" width="40">
                </div>
                <div class="ms-3 w-100">
                  <div class="d-flex justify-content-between">
                    <h6 class="mb-0">{{ $komentar->user->nama ?? 'User' }} 
                      @if($komentar->user->role == 'mahasiswa')
                        <span class="badge bg-label-primary">Mahasiswa</span>
                      @elseif(in_array($komentar->user->role, ['kepala_unit', 'admin_unit']))
                        <span class="badge bg-label-success">Staff</span>
                      @endif
                    </h6>
                    <small class="text-muted">{{ $komentar->created_at->diffForHumans() }}</small>
                  </div>
                  <p class="mt-1 mb-0">{{ $komentar->isi_komentar }}</p>
                </div>
              </div>
            </li>
          @empty
            <li class="text-center">Belum ada komentar.</li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="card">
      <h5 class="card-header">Balas Tiket</h5>
      <div class="card-body">
        <form action="{{ route('admin_unit.tiket.storeKomentar', $tiket->id) }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="isi_komentar" class="form-label">Tulis Balasan:</label>
            <textarea class="form-control" id="isi_komentar" name="isi_komentar" rows="4" placeholder="Tulis balasan atau catatan internal..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Kirim Balasan</button>
        </form>
      </div>
    </div>

  </div>

  <div class="col-md-4 col-lg-3">
    <div class="card">
      <h5 class="card-header">Update Tiket</h5>
      <div class="card-body">
        <form action="{{ route('admin_unit.tiket.update', $tiket->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="status" class="form-label">Ubah Status</label>
            <select class="form-select" id="status" name="status">
              <option value="Dibuka" {{ $tiket->status == 'Dibuka' ? 'selected' : '' }}>Dibuka</option>
              <option value="Sedang Dikerjakan" {{ $tiket->status == 'Sedang Dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
              <option value="Ditutup" {{ $tiket->status == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
              <option value="Selesai" {{ $tiket->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="prioritas" class="form-label">Ubah Prioritas</label>
            <select class="form-select" id="prioritas" name="prioritas">
              <option value="Rendah" {{ $tiket->prioritas == 'Rendah' ? 'selected' : '' }}>Rendah</option>
              <option value="Sedang" {{ $tiket->prioritas == 'Sedang' ? 'selected' : '' }}>Sedang</option>
              <option value="Tinggi" {{ $tiket->prioritas == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
            </select>
          </div>
          <button type="submit" class="btn btn-success w-100">Update Tiket</button>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection