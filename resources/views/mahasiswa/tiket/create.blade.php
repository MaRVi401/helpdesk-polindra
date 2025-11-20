@extends('layouts.layoutMaster')

@section('title', 'Buat Tiket Baru')

@section('content')
<style>
    /* Style tambahan agar kotak preview gambar terlihat rapi (Sama seperti di Show) */
    .image-preview-box {
        padding: 1.5rem;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0; /* Ditambah border agar terlihat jelas di form */
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
                    <form action="{{ route('mahasiswa.tiket.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label" for="layanan_id">Pilih Layanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-files"></i></span>
                                <select class="form-select" id="layanan_id" name="layanan_id" required onchange="toggleSpecificForm()">
                                    <option value="" selected disabled>-- Pilih Layanan --</option>
                                    @foreach($layanans as $layanan)
                                        {{-- Mengirim nama layanan sebagai data attribute untuk diproses JS --}}
                                        <option value="{{ $layanan->id }}" data-nama="{{ $layanan->nama }}">
                                            {{ $layanan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text">Formulir tambahan akan muncul sesuai layanan yang dipilih.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="deskripsi">Deskripsi Singkat / Alasan Pengajuan <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-message-dots"></i></span>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2" placeholder="Jelaskan secara singkat..." required></textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div id="form-ska" class="specific-form d-none">
                            <h6 class="fw-bold text-primary"><i class="ti ti-file-certificate me-1"></i> Detail Surat Keterangan Aktif</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="keperluan" placeholder="Contoh: Beasiswa BI">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="tahun_ajaran" value="{{ date('Y') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="semester" placeholder="1-8">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Keperluan Lainnya (Opsional)</label>
                                    <input type="text" class="form-control" name="keperluan_lainnya">
                                </div>
                            </div>
                        </div>

                        <div id="form-reset" class="specific-form d-none">
                            <h6 class="fw-bold text-danger"><i class="ti ti-lock-open me-1"></i> Detail Reset Akun</h6>
                            <div class="mb-3">
                                <label class="form-label">Aplikasi yang bermasalah <span class="text-danger">*</span></label>
                                <select class="form-select" name="aplikasi">
                                    <option value="sevima">Sevima</option>
                                    <option value="gmail">Gmail Institusi</option>
                                    <option value="office">Office 365</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Detail Masalah Akun <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="deskripsi_detail" rows="3" placeholder="Contoh: Lupa password dan nomor HP pemulihan hilang"></textarea>
                            </div>
                        </div>

                        <div id="form-ubah-data" class="specific-form d-none">
                            <h6 class="fw-bold text-warning"><i class="ti ti-id me-1"></i> Detail Perubahan Data</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap Baru <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="data_nama_lengkap" placeholder="Sesuai KTP/Ijazah">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="data_tmp_lahir">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="data_tgl_lhr">
                                </div>
                            </div>
                        </div>

                        <div id="form-publikasi" class="specific-form d-none">
                            <h6 class="fw-bold text-info"><i class="ti ti-news me-1"></i> Detail Publikasi</h6>
                            <div class="mb-3">
                                <label class="form-label">Judul Publikasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="judul_publikasi" placeholder="Judul Acara / Berita">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" name="kategori">
                                    <option value="Event">Event / Acara</option>
                                    <option value="Berita">Berita Kampus</option>
                                    <option value="Lomba">Lomba / Prestasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konten / Isi Publikasi <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="konten" rows="4"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload Gambar / Poster</label>
                                <!-- ID imgInp digunakan oleh JS untuk trigger preview -->
                                <input type="file" class="form-control" name="gambar" id="imgInp" accept="image/*">
                                <div class="form-text">Format: JPG, PNG. Max 2MB.</div>
                                
                                <!-- AREA PREVIEW GAMBAR (Sama Style dengan Show) -->
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
                                <!-- End Area Preview -->
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i> Kirim Tiket</button>
                        <a href="{{ route('mahasiswa.tiket.index') }}" class="btn btn-label-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Logic untuk Toggle Form Layanan
    function toggleSpecificForm() {
        const forms = document.querySelectorAll('.specific-form');
        forms.forEach(el => {
            el.classList.add('d-none');
            const inputs = el.querySelectorAll('input, select, textarea');
            inputs.forEach(input => input.disabled = true);
        });
        const select = document.getElementById('layanan_id');
        const selectedOption = select.options[select.selectedIndex];
        const namaLayanan = selectedOption.getAttribute('data-nama'); 

        if (!namaLayanan) return;

        let activeFormId = null;
        if (namaLayanan.includes('Surat Keterangan Aktif')) {
            activeFormId = 'form-ska';
        } else if (namaLayanan.includes('Reset Akun')) {
            activeFormId = 'form-reset';
        } else if (namaLayanan.includes('Ubah Data')) {
            activeFormId = 'form-ubah-data';
        } else if (namaLayanan.includes('Publikasi')) {
            activeFormId = 'form-publikasi';
        }

        // Tampilkan form yang sesuai & Enable inputs
        if (activeFormId) {
            const activeForm = document.getElementById(activeFormId);
            activeForm.classList.remove('d-none');
            const inputs = activeForm.querySelectorAll('input, select, textarea');
            inputs.forEach(input => input.disabled = false);
        }
    }

    // 2. Logic untuk Preview Image dengan Style Kotak
    const imgInp = document.getElementById('imgInp');
    const previewBox = document.getElementById('preview-box');
    const previewImg = document.getElementById('preview-img');
    const previewLink = document.getElementById('preview-link');
    const previewBtnLink = document.getElementById('preview-btn-link');

    if (imgInp) {
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                // Membuat URL objek sementara dari file yang dipilih user
                const objectUrl = URL.createObjectURL(file);
                
                previewImg.src = objectUrl;
                
                // Set link agar bisa diklik "Lihat Ukuran Penuh" (membuka di tab baru secara lokal)
                previewLink.href = objectUrl;
                previewBtnLink.href = objectUrl;
                
                // Tampilkan kotaknya
                previewBox.classList.remove('d-none');
            } else {
                // Sembunyikan jika user membatalkan pilih file
                previewBox.classList.add('d-none');
            }
        }
    }
</script>
@endsection