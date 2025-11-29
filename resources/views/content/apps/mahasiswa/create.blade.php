@extends('layouts.layoutMaster')

@section('title', 'Buat Tiket Baru')

@section('content')
<style>
    /* Style tambahan agar kotak preview gambar terlihat rapi */
    .image-preview-box {
        padding: 1.5rem;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0; 
        border-radius: 8px;
        text-align: center;
        margin-top: 15px;
    }
    .img-thumbnail-container {
        display: inline-block;
        border: 1px solid #e2e8f0;
        padding: 6px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        max-width: 100%;
        transition: transform 0.2s;
    }
    .img-thumbnail-container:hover {
        transform: scale(1.01);
    }
    .img-thumbnail-container img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        display: block;
        max-height: 300px; 
        object-fit: contain;
    }
    .img-caption {
        margin-top: 10px;
        font-size: 0.85rem;
        color: #64748b;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Tiket /</span> Buat Tiket Baru
    </h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Formulir Pengajuan Tiket</h5>
                    <small class="text-muted float-end">Isi data dengan benar</small>
                </div>
                <div class="card-body">
                    
                    <form action="{{ route('service-ticket.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. LAYANAN UTAMA --}}
                        <div class="mb-3">
                            <label class="form-label" for="layanan_id">Pilih Layanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-files"></i></span>
                                <select class="form-select @error('layanan_id') is-invalid @enderror" id="layanan_id" name="layanan_id" required onchange="toggleSpecificForm()">
                                    <option value="" disabled {{ old('layanan_id') ? '' : 'selected' }}>-- Pilih Layanan --</option>
                                    @foreach($data_layanan as $layanan)
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

                        <div class="mb-3">
                            <label class="form-label" for="deskripsi">Deskripsi Singkat / Alasan Pengajuan <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-message-dots"></i></span>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="2" placeholder="Jelaskan secara singkat..." required>{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- 2. FORM SKA --}}
                        <div id="form-ska" class="specific-form d-none">
                            <h6 class="fw-bold text-primary"><i class="ti ti-file-certificate me-1"></i> Detail Surat Keterangan Aktif</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('keperluan') is-invalid @enderror" name="keperluan" placeholder="Contoh: Beasiswa BI" value="{{ old('keperluan') }}">
                                    @error('keperluan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tahun_ajaran') is-invalid @enderror" name="tahun_ajaran" value="{{ old('tahun_ajaran', date('Y')) }}">
                                    @error('tahun_ajaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('semester') is-invalid @enderror" name="semester" placeholder="1-8" value="{{ old('semester') }}">
                                    @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Keperluan Lainnya (Opsional)</label>
                                    <input type="text" class="form-control @error('keperluan_lainnya') is-invalid @enderror" name="keperluan_lainnya" value="{{ old('keperluan_lainnya') }}">
                                </div>
                            </div>
                        </div>

                        {{-- 3. FORM RESET AKUN --}}
                        <div id="form-reset" class="specific-form d-none">
                            <h6 class="fw-bold text-danger"><i class="ti ti-lock-open me-1"></i> Detail Reset Akun</h6>
                            <div class="mb-3">
                                <label class="form-label">Aplikasi yang bermasalah <span class="text-danger">*</span></label>
                                <select class="form-select @error('aplikasi') is-invalid @enderror" name="aplikasi">
                                    <option value="sevima" {{ old('aplikasi') == 'sevima' ? 'selected' : '' }}>Sevima</option>
                                    <option value="gmail" {{ old('aplikasi') == 'gmail' ? 'selected' : '' }}>Gmail Institusi</option>
                                    <option value="office" {{ old('aplikasi') == 'office' ? 'selected' : '' }}>Office 365</option>
                                </select>
                                @error('aplikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Detail Masalah Akun <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('deskripsi_detail') is-invalid @enderror" name="deskripsi_detail" rows="3" placeholder="Contoh: Lupa password dan nomor HP pemulihan hilang">{{ old('deskripsi_detail') }}</textarea>
                                @error('deskripsi_detail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- 4. FORM UBAH DATA --}}
                        <div id="form-ubah-data" class="specific-form d-none">
                            <h6 class="fw-bold text-warning"><i class="ti ti-id me-1"></i> Detail Perubahan Data</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap Baru <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('data_nama_lengkap') is-invalid @enderror" name="data_nama_lengkap" placeholder="Sesuai KTP/Ijazah" value="{{ old('data_nama_lengkap') }}">
                                @error('data_nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('data_tmp_lahir') is-invalid @enderror" name="data_tmp_lahir" value="{{ old('data_tmp_lahir') }}">
                                    @error('data_tmp_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('data_tgl_lhr') is-invalid @enderror" name="data_tgl_lhr" value="{{ old('data_tgl_lhr') }}">
                                    @error('data_tgl_lhr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- 5. FORM PUBLIKASI --}}
                        <div id="form-publikasi" class="specific-form d-none">
                            <h6 class="fw-bold text-info"><i class="ti ti-news me-1"></i> Detail Publikasi</h6>
                            <div class="mb-3">
                                <label class="form-label">Judul Publikasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul_publikasi') is-invalid @enderror" name="judul_publikasi" placeholder="Judul Acara / Berita" value="{{ old('judul_publikasi') }}">
                                @error('judul_publikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kategori') is-invalid @enderror" name="kategori" placeholder="Contoh: Event, Berita, Pengumuman" value="{{ old('kategori') }}">
                                @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konten / Isi Publikasi <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('konten') is-invalid @enderror" name="konten" rows="4">{{ old('konten') }}</textarea>
                                @error('konten') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- VALIDASI GAMBAR DI SINI --}}
                            <div class="mb-3">
                                <label class="form-label">Upload Gambar / Poster <span class="text-danger">*</span></label>
                                
                                {{-- Input Gambar dengan class is-invalid jika error --}}
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror" name="gambar" id="imgInp" accept="image/*">
                                
                                {{-- Pesan Error Khusus Gambar --}}
                                @error('gambar')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong> (Maksimal 2MB, Format: JPG/PNG)
                                    </div>
                                @enderror

                                <div class="form-text">Format: JPG, PNG. Max 2MB. Wajib diisi untuk Request Publikasi.</div>
                                
                                <div id="preview-box" class="image-preview-box d-none">
                                    <h6 style="color:#475569; margin-bottom:10px; font-size:0.85rem; font-weight:600; text-transform:uppercase;">Preview Lampiran Gambar</h6>
                                    <div class="img-thumbnail-container">
                                        <a id="preview-link" href="#" target="_blank">
                                            <img id="preview-img" src="#" alt="Preview Gambar">
                                        </a>
                                    </div>
                                    <div class="img-caption">
                                        <a id="preview-btn-link" href="#" target="_blank" class="text-primary" style="font-size: 0.85rem;">
                                            <i class="ti ti-zoom-in"></i> Lihat Ukuran Penuh
                                        </a>
                                    </div>
                                </div>
                                </div>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i> Kirim Tiket</button>
                        <a href="{{ route('service-ticket.index') }}" class="btn btn-label-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Library SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Logic untuk Toggle Form Layanan (DIPERBAIKI: Disable/Enable Input)
    function toggleSpecificForm() {
        // Sembunyikan semua form spesifik & DISABLE inputnya agar tidak divalidasi browser
        const forms = document.querySelectorAll('.specific-form');
        forms.forEach(el => {
            el.classList.add('d-none');
            const inputs = el.querySelectorAll('input, select, textarea');
            inputs.forEach(input => input.disabled = true); // Disable input tersembunyi
        });

        const select = document.getElementById('layanan_id');
        const selectedOption = select.options[select.selectedIndex];
        const namaLayanan = selectedOption.getAttribute('data-nama'); 

        if (!namaLayanan) return;

        let activeFormId = null;
        // Sesuaikan string pencarian dengan nama layanan di database kamu
        if (namaLayanan.includes('Surat Keterangan Aktif')) {
            activeFormId = 'form-ska';
        } else if (namaLayanan.includes('Reset Akun')) {
            activeFormId = 'form-reset';
        } else if (namaLayanan.includes('Ubah Data')) {
            activeFormId = 'form-ubah-data';
        } else if (namaLayanan.includes('Publikasi')) {
            activeFormId = 'form-publikasi';
        }

        // Tampilkan form yang sesuai & ENABLE inputnya
        if (activeFormId) {
            const activeForm = document.getElementById(activeFormId);
            activeForm.classList.remove('d-none');
            const inputs = activeForm.querySelectorAll('input, select, textarea');
            inputs.forEach(input => input.disabled = false); // Enable input yang aktif
        }
    }

    // 2. Logic untuk Preview Image & Validasi Ukuran
    const imgInp = document.getElementById('imgInp');
    const previewBox = document.getElementById('preview-box');
    const previewImg = document.getElementById('preview-img');
    const previewLink = document.getElementById('preview-link');
    const previewBtnLink = document.getElementById('preview-btn-link');

    if (imgInp) {
        imgInp.onchange = evt => {
            const [file] = imgInp.files;
            
            if (file) {
                // --- VALIDASI UKURAN FILE (CLIENT SIDE) ---
                const fileSizeMB = file.size / 1024 / 1024;
                const maxFileSize = 2; // Batas 2MB

                if (fileSizeMB > maxFileSize) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran File Terlalu Besar!',
                        text: 'Ukuran gambar maksimal adalah 2MB. File Anda berukuran ' + fileSizeMB.toFixed(2) + 'MB.',
                        confirmButtonText: 'Ganti Gambar',
                        confirmButtonColor: '#ff9f43',
                        customClass: { confirmButton: 'btn btn-warning' },
                        buttonsStyling: false
                    });
                    imgInp.value = ""; // Reset input
                    previewBox.classList.add('d-none'); // Sembunyikan preview
                    return; 
                }
                // --- END VALIDASI ---

                const objectUrl = URL.createObjectURL(file);
                previewImg.src = objectUrl;
                previewLink.href = objectUrl;
                previewBtnLink.href = objectUrl;
                previewBox.classList.remove('d-none');
            } else {
                previewBox.classList.add('d-none');
            }
        }
    }

    // 3. Logic on Load
    document.addEventListener('DOMContentLoaded', function() {
        toggleSpecificForm(); // Jalankan saat halaman dimuat untuk reset state

        // Cek Parameter URL untuk Error Upload Server-side
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('upload_error')){
             Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar!',
                text: 'Ukuran file melebihi batas server. Silakan kompres file Anda.',
                confirmButtonColor: '#ea5455'
            });
            window.history.replaceState(null, null, window.location.pathname);
        }
    });

    // 4. WARNING POPUP JIKA VALIDASI LARAVEL GAGAL
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal Membuat Tiket!',
            text: 'Mohon periksa kembali inputan Anda yang berwarna merah.',
            confirmButtonText: 'Periksa Inputan',
            confirmButtonColor: '#ea5455',
            customClass: { confirmButton: 'btn btn-danger' },
            buttonsStyling: false
        });
    @endif
</script>
@endsection