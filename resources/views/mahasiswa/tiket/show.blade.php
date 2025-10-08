@extends('layouts.app')
@section('title', 'Detail Tiket: ' . $tiket->no_tiket)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Layanan / Tiket Saya /</span> Detail Tiket</h4>
    <div class="row">
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Detail Tiket</h5>
                    <div class="row mb-2"><div class="col-sm-4 text-muted">Nomor Tiket:</div><div class="col-sm-8"><strong>{{ $tiket->no_tiket }}</strong></div></div>
                    <div class="row mb-2"><div class="col-sm-4 text-muted">Layanan:</div><div class="col-sm-8">{{ $tiket->layanan->nama }}</div></div>
                    <div class="row mb-2"><div class="col-sm-4 text-muted">Unit Terkait:</div><div class="col-sm-8">{{ $tiket->layanan->unit->nama_unit }}</div></div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Status Saat Ini:</div>
                        <div class="col-sm-8">
                            @if($tiket->status == 'Diajukan') <span class="badge bg-label-primary">{{ $tiket->status }}</span>
                            @elseif($tiket->status == 'Diproses') <span class="badge bg-label-info">{{ $tiket->status }}</span>
                            @elseif($tiket->status == 'Selesai') <span class="badge bg-label-success">{{ $tiket->status }}</span>
                            @else <span class="badge bg-label-danger">{{ $tiket->status }}</span> @endif
                        </div>
                    </div>
                    <div class="row"><div class="col-sm-4 text-muted">Deskripsi:</div><div class="col-sm-8" style="white-space: pre-wrap;">{{ $tiket->deskripsi }}</div></div>
                </div>
            </div>
            <div class="card card-action mb-4">
                <div class="card-header align-items-center"><h5 class="card-action-title mb-0">Riwayat Status Tiket</h5></div>
                <div class="card-body">
                    <ul class="timeline ms-2">
                        @foreach($tiket->riwayatStatus->sortBy('created_at') as $riwayat)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">{{ $riwayat->status }}</h6>
                                    <small class="text-muted">{{ $riwayat->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-2">Oleh: <strong>{{ $riwayat->user->name }}</strong></p>
                                <p class="mb-0">{{ $riwayat->komentar }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Komentar / Diskusi</h5></div>
                <div class="card-body">
                    <div class="mb-4" style="max-height: 400px; overflow-y: auto;">
                        @forelse($tiket->komentar->sortBy('created_at') as $komentar)
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0"><div class="avatar avatar-sm"><img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle"></div></div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between"><h6 class="mb-0">{{ $komentar->user->name }}</h6><small class="text-muted">{{ $komentar->created_at->diffForHumans() }}</small></div>
                                    <p class="mb-0">{{ $komentar->komentar }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">Belum ada komentar.</p>
                        @endforelse
                    </div>
                    <form action="{{ route('mahasiswa.tiket.komentar.store', $tiket->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="komentar" class="form-label">Tulis Komentar</label>
                            <textarea class="form-control @error('komentar') is-invalid @enderror" id="komentar" name="komentar" rows="3" placeholder="Tulis komentar Anda..." required></textarea>
                            @error('komentar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100">Kirim Komentar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection