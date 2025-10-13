<!DOCTYPE html>
<html>
<head>
    <title>Buat Tiket Baru</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 800px; margin: 0 auto; }
        h3 { margin-top: 0; font-size: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #4a5568; }
        input, select, textarea { width: 100%; padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 1rem; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 100px; }
        .button-group { display: flex; gap: 10px; margin-top: 2rem; }
        .button { padding: 10px 16px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .button-primary { background-color: #4299e1; color: white; }
        .button-secondary { background-color: #e2e8f0; color: #4a5568; }
        .button:hover { opacity: 0.9; }
        .is-invalid { border-color: #e53e3e; }
        .invalid-feedback { color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-danger { color: #9b2c2c; background-color: #fed7d7; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Formulir Pengajuan Tiket Layanan</h3>
        
        @if(session('error'))
            <p class="alert alert-danger">{{ session('error') }}</p>
        @endif

        <form action="{{ route('mahasiswa.tiket.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="layanan_id">Jenis Layanan</label>
                <select id="layanan_id" name="layanan_id" class="@error('layanan_id') is-invalid @enderror" required>
                    <option value="" disabled selected>-- Pilih Jenis Layanan --</option>
                    @foreach($layanans as $layanan)
                        <option value="{{ $layanan->id }}" data-nama="{{ $layanan->nama }}" {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>
                            {{ $layanan->nama }}
                        </option>
                    @endforeach
                </select>
                @error('layanan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi Awal</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan kebutuhan atau masalah Anda secara detail..." required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div id="specific-fields-container"></div>

            <div class="button-group">
                <button type="submit" class="button button-primary">Ajukan Tiket</button>
                <a href="{{ route('mahasiswa.dashboard') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const layananSelect = document.getElementById('layanan_id');
            const container = document.getElementById('specific-fields-container');
            function renderFields(selectedIndex) {
                let html = '';
                if(selectedIndex > 0) {
                    const selectedOption = layananSelect.options[selectedIndex];
                    const layananNama = selectedOption.getAttribute('data-nama');
                    
                    switch (layananNama) {
                        case 'Surat Keterangan Aktif Kuliah':
                            html = `
                                <div class="form-group">
                                    <label for="keperluan">Keperluan</label>
                                    <input type="text" id="keperluan" name="keperluan" value="{{ old('keperluan') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="tahun_ajaran">Tahun Ajaran</label>
                                    <input type="number" id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran', date('Y')) }}" required placeholder="Contoh: 2024">
                                </div>
                                <div class="form-group">
                                    <label for="semester">Semester</label>
                                    <input type="number" id="semester" name="semester" value="{{ old('semester') }}" required min="1" max="14">
                                </div>
                                 <div class="form-group">
                                    <label for="keperluan_lainnya">Keperluan Lainnya (Opsional)</label>
                                    <input type="text" id="keperluan_lainnya" name="keperluan_lainnya" value="{{ old('keperluan_lainnya') }}">
                                </div>
                            `;
                            break;
                        case 'Reset Akun E-Learning & Siakad':
                            html = `
                                <div class="form-group">
                                    <label for="aplikasi">Aplikasi</label>
                                    <select id="aplikasi" name="aplikasi" required>
                                        <option value="gmail" {{ old('aplikasi') == 'gmail' ? 'selected' : '' }}>Gmail</option>
                                        <option value="office" {{ old('aplikasi') == 'office' ? 'selected' : '' }}>Office</option>
                                        <option value="sevima" {{ old('aplikasi') == 'sevima' ? 'selected' : '' }}>Sevima</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi_reset">Deskripsi Masalah Reset</label>
                                    <textarea id="deskripsi_reset" name="deskripsi_reset" required>{{ old('deskripsi_reset') }}</textarea>
                                </div>
                            `;
                            break;
                    }
                }
                container.innerHTML = html;
            }
            layananSelect.addEventListener('change', function() {
                renderFields(this.selectedIndex);
            });
            if (layananSelect.selectedIndex > 0) {
                renderFields(layananSelect.selectedIndex);
            }
        });
    </script>
</body>
</html>
