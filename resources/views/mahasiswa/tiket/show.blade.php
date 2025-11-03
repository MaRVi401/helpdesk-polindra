@extends('layouts/layoutMaster')

@section('title', 'Detail Tiket')

@section('content')
<div class="row">
  <div class="col-xl-9 col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">Detail Tiket</h5>
            <small class="text-muted">No. Tiket: {{ $tiket->no_tiket }}</small>
          </div>
          <div>
            @php
            $latestStatus = $tiket->riwayatStatus->last();
            $statusClass = '';
            if ($latestStatus) {
            switch ($latestStatus->status) {
            case 'Pending':
            $statusClass = 'bg-label-warning';
            break;
            case 'Opened':
            $statusClass = 'bg-label-info';
            break;
            case 'Closed':
            $statusClass = 'bg-label-success';
            break;
            default:
            $statusClass = 'bg-label-secondary';
            }
            }
            @endphp
            <span class="badge {{ $statusClass }}">{{ $latestStatus ? $latestStatus->status : 'N/A' }}</span>
          </div>
        </div>
      </div>
      <div class="card-body">
        <p><strong>Layanan:</strong> {{ $tiket->layanan->nama }}</p>
        <p><strong>Deskripsi Masalah:</strong></p>
        <div class="border rounded p-3">
          {!! $tiket->deskripsi !!}
        </div>
        <hr>
        <h6>Riwayat Komentar</h6>
        <div class="timeline timeline-border-left">
          
          @forelse ($tiket->komentar as $komentar)
          <div
            class="timeline-item {{ $komentar->pengirim_id == Auth::id() ? 'timeline-item-right' : '' }}">
            <div class="timeline-point timeline-point-info"></div>
            <div class="timeline-event">
              <div class="timeline-header border-bottom mb-3">
                <h6 class="mb-0">{{ $komentar->pengirim->name }}</h6>
                <small
                  class="text-muted">{{ $komentar->created_at->format('d M Y, H:i') }}</small>
              </div>
              <div class="mb-2">
                <p>{{ $komentar->komentar }}</p>
              </div>
            </div>
          </div>
          @empty
          <div class="timeline-item">
            <div class="timeline-point timeline-point-secondary"></div>
            <div class="timeline-event">
              <div class="timeline-header">
                <h6 class="mb-0">Tidak ada komentar</h6>
              </div>
              <div class="mb-2">
                <p>Belum ada balasan atau komentar untuk tiket ini.</p>
              </div>
            </div>
          </div>
          @endforelse

        </div>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-header">
        <h5 class="mb-0">Tambah Komentar</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('mahasiswa.tiket.storeKomentar', $tiket->id) }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="komentar" class="form-label">Komentar Anda</label>
            <textarea class="form-control" id="komentar" name="komentar" rows="4" placeholder="Tulis balasan Anda di sini..."></textarea>
            @error('komentar')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="btn btn-primary">Kirim Komentar</button>
        </form>
      </div>
    </div>
    </div>
  <div class="col-xl-3 col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="pb-2 border-bottom mb-4">Informasi Pemohon</h6>
        <div class="row">
          <div class="col-12">
            <p><strong>Nama:</strong> {{ $tiket->pemohon->name }}</p>
            <p><strong>Email:</strong> {{ $tiket->pemohon->email }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection

@push('scripts')
<script>
  $(function () {
    // Logika JS jika ada
  });
</script>
@endpush