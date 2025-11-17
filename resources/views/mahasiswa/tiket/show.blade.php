@extends('layouts/layoutMaster')

@section('title', 'Detail Tiket')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-chat.css')}}" />
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Tiket /</span> Detail Tiket
</h4>

<div class="row">
  <div class="col-md-5 col-lg-4">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Detail Tiket</h5>
        <hr class="my-3">
        <div class="info-container">
          <ul class="list-unstyled">
            <li class="mb-2">
              <span class="fw-medium me-1">No. Tiket:</span>
              <span>TKT-{{ $tiket->no_tiket }}</span>
            </li>
            <li class="mb-2">
              <span class="fw-medium me-1">Layanan:</span>
              <span>{{ $tiket->layanan->nama }}</span>
            </li>
            <li class="mb-2">
              <span class="fw-medium me-1">Unit:</span>
              <span>{{ $tiket->layanan->unit->nama_unit ?? 'N/A' }}</span>
            </li>
            
            <li class="mb-2">
              <span class="fw-medium me-1">Status:</span>
              @if($tiket->status == 'menunggu')
                <span class="badge bg-label-warning">Menunggu</span>
              @elseif($tiket->status == 'diproses')
                <span class="badge bg-label-info">Diproses</span>
              @elseif($tiket->status == 'selesai')
                <span class="badge bg-label-success">Selesai</span>
              @elseif($tiket->status == 'ditutup')
                <span class="badge bg-label-secondary">Ditutup</span>
              @endif
            </li>
            
            <li class="mb-2">
              <span class="fw-medium me-1">Prioritas:</span>
              @if($tiket->prioritas == 'rendah')
                <span class="badge bg-label-secondary">Rendah</span>
              @elseif($tiket->prioritas == 'sedang')
                <span class="badge bg-label-warning">Sedang</span>
              @elseif($tiket->prioritas == 'tinggi')
                <span class="badge bg-label-danger">Tinggi</span>
              @else
                <span class="badge bg-label-primary">{{ $tiket->prioritas }}</span>
              @endif
            </li>

            <li class="mb-2">
              <span class="fw-medium me-1">Dibuat:</span>
              <span>{{ $tiket->created_at->format('d M Y, H:i') }}</span>
            </li>
            <li class="mb-2">
              <span class="fw-medium me-1">Update Terakhir:</span>
              <span>{{ $tiket->updated_at->format('d M Y, H:i') }}</span>
            </li>
          </ul>
          <div class="d-flex justify-content-center">
            <a href="{{ route('mahasiswa.tiket.index') }}" class="btn btn-primary">Kembali ke Daftar</a>
          </div>
        </div>
      </div>
    </div>

    @if($detail)
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Informasi Tambahan</h5>
        </div>
        <div class="card-body">
          <ul class="list-unstyled">
            
            @if($tiket->layanan->nama == 'Surat Keterangan Aktif Kuliah')
              <li class="mb-2"><span class="fw-medium me-1">Keperluan:</span> <span>{{ $detail->keperluan }}</span></li>
              <li class="mb-2"><span class="fw-medium me-1">Tahun Ajaran:</span> <span>{{ $detail->tahun_ajaran }}</span></li>
              <li class="mb-2"><span class="fw-medium me-1">Semester:</span> <span>{{ $detail->semester }}</span></li>
            
            @elseif($tiket->layanan->nama == 'Reset Akun E-Learning & Siakad' || $tiket->layanan->nama == 'Permintaan Reset Akun E-Mail')
              <li class="mb-2"><span class="fw-medium me-1">Aplikasi:</span> <span class="text-uppercase">{{ $detail->aplikasi }}</span></li>

            @elseif($tiket->layanan->nama == 'Ubah Data Mahasiswa')
              <li class="mb-2"><span class="fw-medium me-1">Nama:</span> <span>{{ $detail->data_nama_lengkap }}</span></li>
              <li class="mb-2"><span class="fw-medium me-1">Tempat Lahir:</span> <span>{{ $detail->data_tmp_lahir }}</span></li>
              <li class="mb-2"><span class="fw-medium me-1">Tgl. Lahir:</span> <span>{{ \Carbon\Carbon::parse($detail->data_tgl_lhr)->format('d M Y') }}</span></li>
            
            @elseif($tiket->layanan->nama == 'Request Publikasi Event')
              <li class="mb-2"><span class="fw-medium me-1">Judul:</span> <span>{{ $detail->judul }}</span></li>
              <li class="mb-2"><span class="fw-medium me-1">Kategori:</span> <span>{{ $detail->kategori }}</span></li>
              <li class="mb-2"><span class="fw-medium me-1">Konten:</span> <div class="mt-1">{!! nl2br(e($detail->konten)) !!}</div></li>
              @if($detail->gambar)
              <li class="mb-2"><span class="fw-medium me-1">Gambar:</span> <a href="{{ Storage::url($detail->gambar) }}" target="_blank">Lihat Gambar</a></li>
              @endif
            
            @endif
          </ul>
        </div>
      </div>
    @endif
    
  </div>

  <div class="col-12 col-lg-8"> 
    
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">Deskripsi Tambahan</h5>
      </div>
      <div class="card-body">
        <div class="chat-history-body">
          <ul class="list-unstyled chat-history m-0">
            <li class="chat-message chat-message-right">
              <div class="d-flex overflow-hidden">
                <div class="chat-message-wrapper flex-grow-1">
                  <div class="chat-message-text">
                    <p class="mb-0">{!! nl2br(e($tiket->deskripsi)) !!}</p>
                    @if($tiket->lampiran)
                      <a href="{{ Storage::url($tiket->lampiran) }}" target="_blank" class="d-block mt-2">
                        <i class="ti ti-paperclip me-1"></i> Lihat Lampiran Awal
                      </a>
                    @endif
                  </div>
                  <div class="text-end text-muted mt-1">
                    <small>{{ $tiket->created_at->format('d M Y, H:i') }}</small>
                  </div>
                </div>
                <div class="user-avatar flex-shrink-0 ms-3">
                  <div class="avatar avatar-sm">
                    <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Riwayat Komentar</h5>
      </div>
      <div class="card-body">
        <div class="chat-history-body" style="max-height: 400px; overflow-y: auto;">
          <ul class="list-unstyled chat-history m-0">
            @forelse($tiket->komentar->sortBy('created_at') as $komentar)
              
              @if($komentar->pengirim_id == Auth::id())
                <li class="chat-message chat-message-right">
                  <div class="d-flex overflow-hidden">
                    <div class="chat-message-wrapper flex-grow-1">
                      <div class="chat-message-text">
                        <p class="mb-0">{!! nl2br(e($komentar->komentar)) !!}</p>
                        @if($komentar->lampiran)
                          <a href="{{ Storage::url($komentar->lampiran) }}" target="_blank" class="d-block mt-2">
                            <i class="ti ti-paperclip me-1"></i> Lihat Lampiran
                          </a>
                        @endif
                      </div>
                      <div class="text-end text-muted mt-1">
                        <small>{{ $komentar->created_at->format('d M Y, H:i') }}</small>
                      </div>
                    </div>
                    <div class="user-avatar flex-shrink-0 ms-3">
                      <div class="avatar avatar-sm">
                        <img src="{{ $komentar->pengirim->avatar ? Storage::url($komentar->pengirim->avatar) : asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                      </div>
                    </div>
                  </div>
                </li>
              
              @else
                <li class="chat-message">
                  <div class="d-flex overflow-hidden">
                    <div class="user-avatar flex-shrink-0 me-3">
                      <div class="avatar avatar-sm">
                        <img src="{{ $komentar->pengirim->avatar ? Storage::url($komentar->pengirim->avatar) : asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                      </div>
                    </div>
                    <div class="chat-message-wrapper flex-grow-1">
                      <div class="chat-message-text">
                        <p class="mb-0">{!! nl2br(e($komentar->komentar)) !!}</p>
                         @if($komentar->lampiran)
                            <a href="{{ Storage::url($komentar->lampiran) }}" target="_blank" class="d-block mt-2">
                              <i class="ti ti-paperclip me-1"></i> Lihat Lampiran
                            </a>
                          @endif
                      </div>
                      <div class="text-muted mt-1">
                        <small>{{ $komentar->pengirim->nama }} - {{ $komentar->created_at->format('d M Y, H:i') }}</small>
                      </div>
                    </div>
                  </div>
                </li>
              @endif
            @empty
              <li class="text-center text-muted mt-3">Belum ada komentar.</li>
            @endforelse
          </ul>
        </div>

        <hr>

        <div class="chat-history-footer">
          @if(!$tiket->jawaban_id)
          <form class="form-send-message d-flex justify-content-between align-items-center" 
                action="{{ route('mahasiswa.tiket.komentar.store', $tiket->id) }}" 
                method="POST" 
                enctype="multipart/form-data">
            @csrf
            <input type="text" class="form-control message-input" name="komentar" placeholder="Ketik balasan Anda..." required>
            <div class="message-actions d-flex align-items-center">
              <label for="lampiran-komentar" class="btn btn-text-secondary btn-icon me-1" data-bs-toggle="tooltip" title="Tambah Lampiran">
                <i class="ti ti-paperclip ti-sm"></i>
              </label>
              <input type="file" id="lampiran-komentar" name="lampiran" class="d-none">
              
              <button type="submit" class="btn btn-primary d-flex send-msg-btn">
                <i class="ti ti-send me-md-1 me-0"></i>
                <span class="align-middle d-md-inline-block d-none">Kirim</span>
              </button>
            </div>
          </form>
            @if($errors->has('komentar') || $errors->has('lampiran'))
            <div class="text-danger mt-2">
                @if($errors->has('komentar'))
                    <small>{{ $errors->first('komentar') }}</small><br>
                @endif
                @if($errors->has('lampiran'))
                    <small>{{ $errors->first('lampiran') }}</small>
                @endif
            </div>
            @endif
          @else
            <div class="alert alert-secondary text-center" role="alert">
              Tiket ini telah ditutup. Anda tidak dapat menambahkan komentar lagi.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection