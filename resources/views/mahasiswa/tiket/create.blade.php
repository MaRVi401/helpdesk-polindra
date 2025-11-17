@extends('layouts/layoutMaster')

@section('title', 'Buat Tiket Baru')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />

<style>
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--bs-body-color) !important;
  }
  .select2-dropdown {
    background-color: var(--bs-body-bg) !important;
    border-color: var(--bs-border-color) !important;
  }
  .select2-container--default .select2-results__option {
    color: var(--bs-body-color) !important;
  }
  .select2-container--default .select2-results__option[aria-selected=true] {
    background-color: var(--bs-primary-bg-subtle) !important;
  }
  .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: var(--bs-primary) !important;
    color: var(--bs-white) !important;
  }
  .select2-search--dropdown .select2-search__field {
    color: var(--bs-body-color) !important;
    background-color: var(--bs-card-bg) !important;
    border-color: var(--bs-border-color) !important;
  }
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/forms-selects.js')}}"></script>
<script>
  $(document).ready(function() {
    $('.datepicker').flatpickr({
      dateFormat: 'Y-m-d'
    });
  });
</script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Tiket /</span> Buat Tiket Baru
</h4>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Formulir Pengajuan Tiket Bantuan</h5>
      </div>
      <div class="card-body">
        
        @if ($errors->any())
          <div class="alert alert-danger">
            <p class="mb-1"><strong>Oops! Terjadi kesalahan:</strong></p>
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if(!$layananTerpilih)
          
          <p>Silakan pilih jenis layanan yang Anda butuhkan untuk melanjutkan.</p>
          <form id="form-pilih-layanan" action="{{ route('mahasiswa.tiket.show-create-form') }}" method="GET">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label" for="layanan_id">Jenis Layanan</label>
                <select id="layanan_id" name="layanan_id" class="select2 form-select" data-placeholder="Pilih Layanan" required>
                  <option value="" selected disabled>Pilih Layanan</option>
                  @foreach($layanans as $layanan)
                    <option value="{{ $layanan->id }}">
                      {{ $layanan->nama }} ({{ $layanan->unit->nama_unit ?? 'Tanpa Unit' }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 text-end">
                <a href="{{ route('mahasiswa.tiket.index') }}" class="btn btn-label-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Lanjut</button>
              </div>
            </div>
          </form>

        @else
          
          <form id="form-buat-tiket" action="{{ route('mahasiswa.tiket.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="layanan_id" value="{{ $layananTerpilih->id }}">

            <div class="row g-3 mb-3">
              <div class="col-12">
                <label class="form-label">Layanan Dipilih:</label>
                <input type="text" class="form-control" 
                       value="{{ $layananTerpilih->nama }} ({{ $layananTerpilih->unit->nama_unit ?? 'Tanpa Unit' }})" 
                       disabled readonly />
              </div>
            </div>

            <hr class="my-4">
            
            @if($layananTerpilih->nama == 'Surat Keterangan Aktif Kuliah')
            <div id="form-surat-aktif">
              <h5 class="mb-3">Detail Surat Keterangan Aktif</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label" for="keperluan">Keperluan</label>
                  <input type="text" id="keperluan" name="keperluan" class="form-control" placeholder="Contoh: Tunjangan Gaji" value="{{ old('keperluan') }}" required />
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                  <input type="text" id="tahun_ajaran" name="tahun_ajaran" class="form-control" placeholder="Contoh: 2024" value="{{ old('tahun_ajaran') }}" required />
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="semester">Semester</label>
                  <input type="number" id="semester" name="semester" class="form-control" placeholder="Contoh: 5" value="{{ old('semester') }}" required />
                </div>
              </div>
            </div>

            @elseif($layananTerpilih->nama == 'Reset Akun E-Learning & Siakad' || $layananTerpilih->nama == 'Permintaan Reset Akun E-Mail')
            <div id="form-reset-akun">
              <h5 class="mb-3">Detail Reset Akun</h5>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label" for="aplikasi">Aplikasi</label>
                  <select id="aplikasi" name="aplikasi" class="form-select" required>
                    <option value="" selected disabled>Pilih Aplikasi</option>
                    <option value="gmail" {{ old('aplikasi') == 'gmail' ? 'selected' : '' }}>Email (Gmail)</option>
                    <option value="office" {{ old('aplikasi') == 'office' ? 'selected' : '' }}>Office 365</option>
                    <option value="sevima" {{ old('aplikasi') == 'sevima' ? 'selected' : '' }}>SIAKAD (Sevima)</option>
                  </select>
                </div>
              </div>
            </div>

            @elseif($layananTerpilih->nama == 'Ubah Data Mahasiswa')
            <div id="form-ubah-data">
              <h5 class="mb-3">Detail Perubahan Data Mahasiswa</h5>
              <p>Masukkan data yang BENAR. (Lampirkan KTP/KK/Ijazah di field Lampiran di bawah)</p>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="data_nama_lengkap">Nama Lengkap (Sesuai Ijazah)</label>
                  <input type="text" id="data_nama_lengkap" name="data_nama_lengkap" class="form-control" value="{{ old('data_nama_lengkap') }}" required />
                </div>
                <div class="col-md-3">
                  <label class="form-label" for="data_tmp_lahir">Tempat Lahir</label>
                  <input type="text" id="data_tmp_lahir" name="data_tmp_lahir" class="form-control" value="{{ old('data_tmp_lahir') }}" required />
                </div>
                <div class="col-md-3">
                  <label class="form-label" for="data_tgl_lhr">Tanggal Lahir</label>
                  <input type="text" id="data_tgl_lhr" name="data_tgl_lhr" class="form-control datepicker" placeholder="YYYY-MM-DD" value="{{ old('data_tgl_lhr') }}" required />
                </div>
              </div>
            </div>

            @elseif($layananTerpilih->nama == 'Request Publikasi Event')
            <div id="form-req-publikasi">
              <h5 class="mb-3">Detail Request Publikasi</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="judul_publikasi">Judul Publikasi</label>
                  <input type="text" id="judul_publikasi" name="judul_publikasi" class="form-control" value="{{ old('judul_publikasi') }}" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="kategori_publikasi">Kategori</label>
                  <input type="text" id="kategori_publikasi" name="kategori_publikasi" class="form-control" placeholder="Contoh: Berita, Event, Prestasi" value="{{ old('kategori_publikasi') }}" required />
                </div>
                <div class="col-12">
                  <label class="form-label" for="konten">Konten/Naskah Berita</label>
                  <textarea id="konten" name="konten" class="form-control" rows="4" required>{{ old('konten') }}</textarea>
                </div>
                <div class="col-12">
                  <label class="form-label" for="gambar_publikasi">Gambar (Opsional)</label>
                  <input type="file" id="gambar_publikasi" name="gambar_publikasi" class="form-control" />
                </div>
              </div>
            </div>
            @endif
            
            <hr class="my-4">

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi Tambahan</label>
                <p class="form-text">Jelaskan detail masalah atau permintaan Anda. (Wajib diisi)</p>
                <textarea 
                  class="form-control" 
                  id="deskripsi" 
                  name="deskripsi" 
                  rows="6" 
                  placeholder="Jelaskan detail masalah atau permintaan Anda..." 
                  required>{{ old('deskripsi') }}</textarea>
              </div>

              <div class="col-12">
                <label class="form-label" for="lampiran">Lampiran (Opsional)</label>
                <input type="file" id="lampiran" name="lampiran" class="form-control" />
                <div class="form-text">Maksimal 5MB. Format: jpg, png, pdf, docx, xlsx.</div>
              </div>

              <div class="col-12 text-end">
                <a href="{{ route('mahasiswa.tiket.show-create-form') }}" class="btn btn-label-secondary me-2">Ganti Layanan</a>
                <button type="submit" class="btn btn-primary">Kirim Tiket</button>
              </div>
            </div>
          </form>
        @endif
        
      </div>
    </div>
  </div>
</div>
@endsection