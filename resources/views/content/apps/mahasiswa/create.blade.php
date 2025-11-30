@php
  use Illuminate\Support\Facades\Auth;
@endphp
@extends('layouts.layoutMaster')
@section('title', 'Buat Tiket Layanan')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/management/service-ticket/create.js')
@endsection
@section('content')
  <div class="row">
    <div class="col-xl-4 mb-5">
      <div class="card h-100">
        <div class="d-flex align-items-end row h-100">
          <div class="col-7">
            <div class="card-body text-nowrap d-flex flex-column h-100">
              <div>
                <h5 class="card-title mb-0">Hai!, {{ Auth::user()->name }} 😊</h5>
                <p class="mb-2">Total permohonan tiket kamu</p>
                <h4 class="text-primary mb-1">{{ $total_tiket }} Tiket</h4>
              </div>
              <div class="mt-auto pt-3">
                <a href="{{ route('service-ticket.index') }}" class="btn btn-primary">
                  Layanan Saya <i class="icon-base ti tabler-trending-up ms-2"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="col-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-4 h-100 d-flex align-items-end">
              <img src="{{ asset('assets/img/illustrations/student.png') }}" alt="Student Illustration"
                class="img-fluid" />
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- BANNER --}}
    <div class="col-xl-8 col-md-12 mb-5">
      <div class="card h-100">
        <div class="user-profile-header-banner h-100">
          <img src="{{ asset('assets/img/pages/service-banner.png') }}" alt="Service Banner"
            class="rounded w-100 h-100" />
        </div>
      </div>
    </div>
    {{-- FORM LAYANAN --}}
    <div class="col-xl">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Formulir Permohonan Tiket</h5>
          <small class="text-muted float-end text-warning">Isi data dengan benar</small>
        </div>
        <div class="card-body">
          <form id="form-create-ticket" action="{{ route('service-ticket.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            {{-- 1. LAYANAN UTAMA --}}
            <div class="mb-3">
              <label class="form-label" for="layanan_id">Pilih Layanan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="icon-base ti tabler-layout"></i></span>
                <select class="form-select @error('layanan_id') is-invalid @enderror" id="layanan_id" name="layanan_id"
                  required>
                  <option value="" disabled {{ old('layanan_id') ? '' : 'selected' }}>Pilih Layanan</option>
                  @foreach ($data_layanan as $layanan)
                    <option value="{{ $layanan->id }}" data-nama="{{ $layanan->nama }}"
                      {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>
                      {{ $layanan->nama }}
                    </option>
                  @endforeach
                </select>
                @error('layanan_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-text">Formulir tambahan akan muncul sesuai layanan yang dipilih.</div>
            </div>
            {{-- DESKRIPSI --}}
            <div class="mb-3">
              <label class="form-label" for="deskripsi">Deskripsi / Alasan Permohonan</label>
              <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="2"
                placeholder="Jelaskan maksud permohonan kamu..." required>{{ old('deskripsi') }}</textarea>
              @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <hr class="my-4">
            {{-- 2. FORM SKA --}}
            <div id="form-ska" class="specific-form d-none">
              <h6 class="fw-bold text-primary"><i class="icon-base ti tabler-navigation-share"></i> Detail Surat
                Keterangan Aktif
              </h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Keperluan</label>
                  <input type="text" class="form-control @error('keperluan') is-invalid @enderror" name="keperluan"
                    placeholder="Contoh: Beasiswa perguruan tinggi" value="{{ old('keperluan') }}" required>
                  @error('keperluan')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Tahun Ajaran</label>
                  <input type="number" class="form-control @error('tahun_ajaran') is-invalid @enderror"
                    name="tahun_ajaran" value="{{ old('tahun_ajaran', date('Y')) }}" required>
                  @error('tahun_ajaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Semester</label>
                  <select class="form-select @error('semester') is-invalid @enderror" name="semester" required>
                    <option value="" disabled selected>Pilih Semester</option>
                    @for ($i = 1; $i <= 8; $i++)
                      <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester
                        {{ $i }}</option>
                    @endfor
                  </select>
                  @error('semester')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Keperluan Lainnya (Opsional)</label>
                  <input type="text" class="form-control @error('keperluan_lainnya') is-invalid @enderror"
                    name="keperluan_lainnya" value="{{ old('keperluan_lainnya') }}"
                    placeholder="Tuliskan keperluan lainnya di sini...">
                </div>
              </div>
            </div>
            {{-- 3. FORM RESET AKUN --}}
            <div id="form-reset" class="specific-form d-none">
              <h6 class="fw-bold text-danger"><i class="icon-base ti tabler-navigation-share"></i> Detail Reset Akun</h6>
              <div class="mb-3">
                <label class="form-label">Aplikasi</label>
                <select class="form-select @error('aplikasi') is-invalid @enderror" name="aplikasi" required>
                  <option value="" disabled selected>Pilih Aplikasi</option>
                  <option value="sevima" {{ old('aplikasi') == 'sevima' ? 'selected' : '' }}>Sevima</option>
                  <option value="gmail" {{ old('aplikasi') == 'gmail' ? 'selected' : '' }}>Gmail Institusi</option>
                  <option value="office" {{ old('aplikasi') == 'office' ? 'selected' : '' }}>Office 365</option>
                </select>
                @error('aplikasi')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label">Detail Masalah Akun</label>
                <textarea class="form-control @error('deskripsi_detail') is-invalid @enderror" name="deskripsi_detail" rows="3"
                  placeholder="Contoh: Lupa password akun office" required>{{ old('deskripsi_detail') }}</textarea>
                @error('deskripsi_detail')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            {{-- 4. FORM UBAH DATA --}}
            <div id="form-ubah-data" class="specific-form d-none">
              <h6 class="fw-bold text-warning"><i class="icon-base ti tabler-navigation-share"></i> Detail Perubahan
                Data</h6>
              <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control @error('data_nama_lengkap') is-invalid @enderror"
                  name="data_nama_lengkap" placeholder="Sesuai KTP/Ijazah" value="{{ old('data_nama_lengkap') }}"
                  required>
                @error('data_nama_lengkap')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tempat Lahir</label>
                  <input type="text" class="form-control @error('data_tmp_lahir') is-invalid @enderror"
                    name="data_tmp_lahir" value="{{ old('data_tmp_lahir') }}" placeholder="Contoh: Indramayu"
                    required>
                  @error('data_tmp_lahir')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Lahir</label>
                  <input type="date" class="form-control @error('data_tgl_lhr') is-invalid @enderror"
                    name="data_tgl_lhr" value="{{ old('data_tgl_lhr') }}" required>
                  @error('data_tgl_lhr')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
            {{-- 5. FORM PUBLIKASI --}}
            <div id="form-publikasi" class="specific-form d-none">
              <h6 class="fw-bold text-info"><i class="icon-base ti tabler-navigation-share"></i>Detail Publikasi</h6>
              <div class="mb-3">
                <label class="form-label">Judul Publikasi</label>
                <input type="text" class="form-control @error('judul_publikasi') is-invalid @enderror"
                  name="judul_publikasi" placeholder="Judul Acara / Berita" value="{{ old('judul_publikasi') }}"
                  required>
                @error('judul_publikasi')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              {{-- KATEGORI --}}
              <div class="mb-3">
                <label class="form-label">Kategori</label>
                <input type="text" class="form-control @error('kategori') is-invalid @enderror" name="kategori"
                  placeholder="Contoh: Event, Berita, Pengumuman" value="{{ old('kategori') }}" required>
                @error('kategori')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              {{-- KONTEN --}}
              <div class="mb-3">
                <label class="form-label">Konten / Isi Publikasi</label>
                <textarea class="form-control @error('konten') is-invalid @enderror" name="konten" rows="4"
                  placeholder="Isi Publikasi..." required>{{ old('konten') }}</textarea>
                @error('konten')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              {{-- VALIDASI GAMBAR DI SINI --}}
              <div class="mb-3">
                <label class="form-label">Upload Gambar / Poster</label>
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" name="gambar"
                  id="imgInp" accept="image/*" required>
                {{-- Pesan Error Khusus Gambar --}}
                @error('gambar')
                  <div class="invalid-feedback d-block">
                    <strong>{{ $message }}</strong> (Maksimal 2MB, Format: JPG/PNG)
                  </div>
                @enderror
                <div class="form-text text-warning">Format: JPG, PNG. Max 2MB. Wajib diisi untuk Request Publikasi.</div>
                <div id="preview-box" class="d-none mt-3 p-3 bg-light border rounded text-center">
                  <h6 class="text-secondary mb-3 text-uppercase small fw-semibold">Preview Lampiran Gambar</h6>
                  <div class="d-inline-block p-2 bg-white border rounded shadow-sm">
                    <a id="preview-link" href="#" target="_blank">
                      <img id="preview-img" src="#" alt="Preview Gambar" class="img-fluid rounded"
                        style="max-height: 300px;">
                    </a>
                  </div>
                  <div class="mt-2">
                    <a id="preview-btn-link" href="#" target="_blank" class="text-primary small"></a>
                  </div>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary me-3">Kirim Tiket</button>
            <button type="reset" class="btn btn-outline-secondary">
              <i class="icon-base ti tabler-reload me-1"></i>
              Ulang
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
