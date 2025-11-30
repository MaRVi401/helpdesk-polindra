@use('Illuminate\Support\Str')
@extends('layouts/contentNavbarLayout')


@section('title', 'Detail Tiket #' . $tiket->no_tiket)

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- TIMER --}}
    @php
      $cacheKey = 'tiket_timer_' . $tiket->id;
      $deadline = \Illuminate\Support\Facades\Cache::get($cacheKey);
      $isTimerActive = $tiket->statusTerbaru?->status === 'Diselesaikan_oleh_PIC' && $deadline;
    @endphp
    @if ($isTimerActive)
      <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
          <h6 class="mb-0 text-white">Batas Konfirmasi</h6>
        </div>
        <div class="card-body pt-3 text-center">
          <p class="mb-1 text-warning small">Tiket ini akan otomatis ditutup jika tidak ada respon dalam</p>
          <hr class="my-2">
          <div id="admin-countdown" class="p-1 mb-1 fw-bold fs-5">
            Memuat...
          </div>
          <small class="text-muted mb-2">
            {{ $deadline ? \Carbon\Carbon::parse($deadline)->translatedFormat('d F Y, H:i:s') : '-' }}
          </small>
          <hr class="my-2">
          <div class="collapse" id="settingTimer">
            <form action="{{ route('admin_unit.tiket.updateTimer', $tiket->id) }}" method="POST"
              class="bg-light p-2 rounded border">
              @csrf
              @method('PUT')
              <div class="input-group input-group-sm">
                <input type="number" name="amount" class="form-control" placeholder="Nilai" min="1"
                  value="1" required>
                <select name="unit" class="form-select" style="max-width: 90px;">
                  <option value="days">Hari</option>
                  <option value="hours">Jam</option>
                </select>
                <button class="btn btn-primary" type="submit">Set</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          var deadlineStr = "{{ \Carbon\Carbon::parse($deadline)->format('Y-m-d H:i:s') }}";
          var countDownDate = new Date(deadlineStr).getTime();

          var x = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("admin-countdown").innerHTML =
              days + " Hari " + hours + " Jam " + minutes + " Menit " + seconds + " Detik";

            if (distance < 0) {
              clearInterval(x);
              document.getElementById("admin-countdown").innerHTML = "WAKTU HABIS - Sedang Memproses...";
              setTimeout(function() {
                location.reload();
              }, 2000);
            }
          }, 1000);
        });
      </script>
    @endif

    <div class="row">
      <div class="col-md-8">
        {{-- TINDAK LANJUT --}}
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tindakan Tiket Layanan</h5>
            <span class="badge bg-label-primary">{{ $tiket->layanan->unit->nama_unit ?? '-' }}</span>
          </div>
          <div class="card-body">
            {{-- Alert Informasi Status --}}
            @if ($isFormDisabled)
              <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                <i class="icon-base ti tabler-circle-dashed-check me-2"></i>
                <div>{{ $statusMessage ?? 'Menunggu respon...' }}</div>
              </div>
            @elseif($tiket->statusTerbaru?->status == 'Dinilai_Belum_Selesai_oleh_Pemohon')
              <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                <div>
                  <strong>Perhatian:</strong> Mahasiswa menolak penyelesaian tiket ini<br>
                  Total Penolakan: <strong>{{ $rejectionCount }}x</strong>.
                </div>
              </div>
            @endif
            <form action="{{ route('admin_unit.tiket.update', $tiket->id) }}" method="POST">
              @csrf
              @method('PUT')
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-bold">Status Pengerjaan</label>
                  <select name="status" class="form-select" {{ $isFormDisabled ? 'disabled' : '' }} required>
                    @if ($isFormDisabled)
                      <option selected>{{ str_replace('_', ' ', $tiket->statusTerbaru?->status) }}</option>
                    @else
                      <option value="" selected disabled>Pilih Tindakan Selanjutnya</option>
                      @foreach ($nextOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                      @endforeach
                    @endif
                  </select>
                  @if (!$isFormDisabled)
                    <div class="form-text text-muted">
                      Pilih status berikutnya untuk memproses tiket ini.
                    </div>
                  @endif
                </div>
                <div class="col-12 mt-3">
                  <button type="submit" class="btn btn-primary w-100" {{ $isFormDisabled ? 'disabled' : '' }}>
                    <i class="bx bx-save me-1"></i> Simpan Status
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        {{-- KOMENTAR --}}
        <div class="card mb-4">
          <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Komentar Tiket</h5>
            <span class="badge bg-label-secondary">{{ $tiket->komentar->count() }} Komentar</span>
          </div>
          <div class="card-body pt-4">
            <form action="{{ route('admin_unit.tiket.komentar', $tiket->id) }}" method="POST" class="mb-4">
              @csrf
              <div class="mb-3">
                <textarea name="komentar" class="form-control" rows="2"
                  placeholder="Tulis pesan untuk pemohon atau catatan pengerjaan..." required
                  {{ in_array($tiket->statusTerbaru?->status, ['Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon', 'Ditolak']) ? 'disabled' : '' }}></textarea>
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-primary"
                  {{ in_array($tiket->statusTerbaru?->status, ['Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon', 'Ditolak']) ? 'disabled' : '' }}>
                  Kirim Komentar
                </button>
              </div>
            </form>
            <hr class="my-4">
            <div class="timeline">
              @forelse($tiket->komentar->sortByDesc('created_at') as $komen)
                @php
                  $role = $komen->pengirim->role ?? 'unknown';
                  $userName = $komen->pengirim->name;
                  $dotClass = 'dot-' . $role;
                  $isMe = $komen->pengirim_id == Auth::id();
                @endphp
                <div class="timeline-item">
                  <span class="timeline-dot {{ $dotClass }}"></span>
                  <div class="timeline-time">{{ $komen->created_at->translatedFormat('d F Y, H:i') }}</div>
                  <div class="timeline-title">
                    {{ $userName }}
                    <span style="font-size:0.8em">({{ ucwords(strtolower(str_replace('_', ' ', trim($role)))) }})</span>
                  </div>
                  <div class="border timeline-body-comment rounded-3 p-2">
                    <p class="mb-0 text-break">{!! nl2br(e($komen->komentar)) !!}</p>
                  </div>

                </div>
              @empty
                <div class="text-center py-5">
                  <i class="bx bx-message-rounded-dots fs-1 text-muted mb-2"></i>
                  <p class="text-muted">Belum ada komentar pada tiket ini.</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>
        {{-- RIWAYAT STATUS --}}
        <div class="card mb-4">
          <div class="card-header border-bottom py-3">
            <h5 class="mb-0">Riwayat Status</h5>
          </div>
          <div class="card-body pt-4">
            <div class="timeline">
              @forelse($tiket->riwayatStatus->sortByDesc('created_at') as $riwayat)
                @php
                  $role = $riwayat->user->role ?? 'system';
                  $userName = $riwayat->user->name ?? 'Sistem';
                  $dotClass = 'dot-' . $role;

                  $statusClass = match ($riwayat->status) {
                      'Diajukan_oleh_Pemohon' => 'bg-label-status-pending',
                      'Ditangani_oleh_PIC' => 'bg-label-status-process',
                      'Diselesaikan_oleh_PIC' => 'bg-label-status-review',
                      'Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon' => 'bg-label-status-completed',
                      'Dinilai_Belum_Selesai_oleh_Pemohon',
                      'Pemohon_Bermasalah',
                      'Ditolak'
                          => 'bg-label-status-rejected',
                      default => 'text-muted',
                  };
                @endphp
                <div class="timeline-item">
                  <span class="timeline-dot {{ $dotClass }}"></span>
                  <div class="timeline-time">
                    {{ $riwayat->created_at->translatedFormat('d F Y, H:i') }}
                  </div>
                  <div class="timeline-title d-flex flex-wrap align-items-center gap-2">
                    <span class="fw-bold">{{ $userName }}</span>
                    @if ($role !== 'system')
                      <span style="font-size: 0.7rem;">
                        ({{ ucwords(strtolower(str_replace('_', ' ', trim($role)))) }})
                      </span>
                    @endif
                  </div>
                  <div class="timeline-body mt-1">
                    Status Layanan :
                    <span class="badge {{ $statusClass }}">
                      {{ str_replace('_', ' ', $riwayat->status) }}
                    </span>
                  </div>
                </div>
              @empty
                <p class="text-center text-muted  mb-0">Belum ada riwayat status.</p>
              @endforelse
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        {{-- DATA PEMOHON --}}
        <div class="card mb-4">
          <div class="card-header border-bottom py-3">
            <h5 class="mb-0">Data Pemohon</h5>
          </div>
          <div class="card-body text-center pt-4">
            @php
              $pemohon = $tiket->pemohon ?? null;
              $avatarUrl = null;
              if ($pemohon) {
                  $avatarUrl = $pemohon->avatar
                      ? asset('storage/avatar/' . $pemohon->avatar)
                      : $pemohon->profile_photo_url ?? null;
              }
            @endphp
            <div class="avatar avatar-xl mx-auto mb-2 text-center">
              @if (!empty($avatarUrl))
                <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle" />
              @else
                <span
                  class="avatar-initial rounded-circle bg-label-info d-inline-flex align-items-center justify-content-center"
                  style="width:56px;height:56px;font-weight:600;">
                  {{ strtoupper(substr($pemohon->name ?? 'U', 0, 2)) }}
                </span>
              @endif
            </div>
            <h5 class="mb-1">{{ $tiket->pemohon->name }}</h5>
            <p class="text-muted mb-0 small">{{ $tiket->pemohon->email }}</p>
            @if ($tiket->pemohon->mahasiswa)
              <hr>
              <div class="text-start small">
                <div> <span>NIM - {{ $tiket->pemohon->mahasiswa->nim }}</span></div>
                <div> <span
                    class="badge bg-label-primary">{{ $tiket->pemohon->mahasiswa->programStudi->program_studi ?? '-' }}</span>
                </div>
              </div>
          </div>
          @endif
        </div>

        {{-- INFORMASI TIKET --}}
        <div class="card mb-4">
          <div class="card-header border-bottom py-3">
            <h5 class="mb-0">Informasi Tiket</h5>
          </div>
          <div class="card-body pt-3 pb-3">
            <div class="mb-3">
              <span class="text-muted d-block">Nama Layanan</span>
              <small class="mb-0">{{ $tiket->layanan->nama }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">No Tiket</span>
              <small class="mb-0">{{ $tiket->no_tiket }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Prioritas</span>
              @php
                $prioVal = $tiket->layanan->prioritas ?? 2;
                $prioBadge = match ($prioVal) {
                    3 => 'bg-label-danger',
                    2 => 'bg-label-warning',
                    default => 'bg-label-info',
                };
                $prioLabel = match ($prioVal) {
                    3 => 'Tinggi',
                    2 => 'Sedang',
                    default => 'Rendah',
                };
              @endphp
              <span class="badge {{ $prioBadge }}">{{ $prioLabel }}</span>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Status</span>
              @php
                $statusBadge = match ($tiket->statusTerbaru?->status ?? 'Diajukan_oleh_Pemohon') {
                    'Diajukan_oleh_Pemohon' => 'bg-label-status-pending',
                    'Ditangani_oleh_PIC' => 'bg-label-status-process',
                    'Diselesaikan_oleh_PIC' => 'bg-label-status-review',
                    'Dinilai_Selesai_oleh_Kepala', 'Dinilai_Selesai_oleh_Pemohon' => 'bg-label-status-completed',
                    'Dinilai_Belum_Selesai_oleh_Pemohon', 'Pemohon_Bermasalah', 'Ditolak' => 'bg-label-status-rejected',
                    default => 'text-muted',
                };
              @endphp
              <small
                class="badge {{ $statusBadge }}">{{ str_replace('_', ' ', $tiket->statusTerbaru?->status ?? 'Menunggu') }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Penanggungjawab (PIC)</span>
              @php
                $pic =
                    $tiket->riwayatStatus->where('status', 'Ditangani_oleh_PIC')->first()?->user?->name ??
                    'Menunggu penanganan...';
              @endphp
              <small>{{ $pic }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Dibuat pada</span>
              <small class="mb-0 small">{{ $tiket->created_at->translatedFormat('d F Y H:i') }}</small>
            </div>
            <hr class="my-2">
            <div class="mb-3">
              <span class="text-muted d-block">Deskripsi</span>
              <small class="mb-0 small">{{ $tiket->deskripsi }}</small>
            </div>
          </div>
        </div>

        {{-- DETAIL LAYANAN SPESIFIK --}}
        @if (isset($detailLayanan))
          <div class="card mb-4">
            <div class="card-header border-bottom py-3">
              <h5 class="mb-0">Detail Layanan</h5>
            </div>
            <div class="card-body pt-3 pb-3">
              {{-- 1. Surat Keterangan Aktif Kuliah --}}
              @if (Str::contains($tiket->layanan->nama, 'Surat Keterangan Aktif Kuliah'))
                <div class="mb-3">
                  <span class="text-muted d-block">Keperluan</span>
                  <small class="mb-0">{{ $detailLayanan->keperluan }}</small>
                </div>
                <hr class="my-2">
                <div class="mb-3">
                  <span class="text-muted d-block">Tahun Ajaran</span>
                  <small class="mb-0">{{ $detailLayanan->tahun_ajaran }}</small>
                </div>
                <hr class="my-2">
                <div class="mb-3">
                  <span class="text-muted d-block">Semester</span>
                  <small class="mb-0">{{ $detailLayanan->semester }}</small>
                </div>
                <hr class="my-2">
                @if ($detailLayanan->keperluan_lainnya)
                  <div>
                    <span class="text-muted d-block">Keperluan Lainnya</span>
                    <small class="mb-0">{{ $detailLayanan->keperluan_lainnya }}</small>
                  </div>
                @endif
                {{-- 2. Reset Akun --}}
              @elseif(Str::contains($tiket->layanan->nama, 'Reset Akun'))
                <div class="mb-3">
                  <span class="text-muted d-block">Aplikasi</span>
                  <small class="mb-0">{{ $detailLayanan->aplikasi }}</small>
                </div>
                <hr class="my-2">
                <div>
                  <span class="text-muted d-block">Deskripsi Masalah</span>
                  <small class="mb-0">{{ $detailLayanan->deskripsi }}</small>
                </div>
                {{-- 3. Ubah Data Mahasiswa --}}
              @elseif(Str::contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                <div class="mb-3">
                  <span class="text-muted d-block">Nama Lengkap</span>
                  <small class="mb-0">{{ $detailLayanan->data_nama_lengkap ?? '-' }}</small>
                </div>
                <hr class="my-2">
                <div class="mb-3">
                  <span class="text-muted d-block">Tempat Lahir</span>
                  <small class="mb-0">{{ $detailLayanan->data_tmp_lahir ?? '-' }}</small>
                </div>
                <hr class="my-2">
                <div>
                  <span class="text-muted d-block">Tanggal Lahir</span>
                  <small
                    class="mb-0">{{ $detailLayanan->data_tgl_lhr ? date('d-m-Y', strtotime($detailLayanan->data_tgl_lhr)) : '-' }}</small>
                </div>
                {{-- 4. Request Publikasi --}}
              @elseif(Str::contains($tiket->layanan->nama, 'Request Publikasi') || Str::contains($tiket->layanan->nama, 'Publikasi'))
                <div class="mb-3">
                  <span class="text-muted d-block">Judul / Topik</span>
                  <small class="mb-0">{{ $detailLayanan->judul }}</small>
                </div>
                <hr class="my-2">
                <div class="mb-3">
                  <span class="text-muted d-block">Kategori</span>
                  <small class="mb-0">{{ $detailLayanan->kategori }}</small>
                </div>
                <hr class="my-2">

                <div class="mb-3">
                  <span class="text-muted d-block">Konten / Isi</span>
                  <small class="mb-0">{!! nl2br(e($detailLayanan->konten)) !!}</small>
                </div>
                <hr class="my-2">

                @if (isset($detailLayanan->gambar) && $detailLayanan->gambar)
                  <div class="mb-2">
                    <span class="text-muted d-block mb-2">Lampiran Gambar</span>
                    <div class="text-center border rounded p-2 bg-light mb-2">
                      <img src="{{ asset('storage/' . $detailLayanan->gambar) }}" alt="Preview"
                        class="img-fluid rounded" style="max-height: 150px;">
                    </div>
                    <a href="{{ asset('storage/' . $detailLayanan->gambar) }}" target="_blank"
                      class="btn btn-sm btn-primary w-100">
                      <i class='bx bx-image me-1'></i> Lihat Gambar Penuh
                    </a>
                  </div>
                @endif

              @endif
            </div>
          </div>
        @endif
      </div>

      <div class="mt-3">
        <a href="{{ route('admin_unit.tiket.index') }}" class="btn btn-primary w-100">
          Semua Daftar Tiket Layanan
          <i class="icon-base ti tabler-trending-up ms-2"></i>
        </a>
      </div>
    </div>
  </div>
  </div>
@endsection
