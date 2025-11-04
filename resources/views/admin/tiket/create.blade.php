<!DOCTYPE html>
<html>
<head>
    <title>Tambah Tiket Baru</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .main-container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 800px; margin: 0 auto; }
        h1 { margin-top: 0; }
        .button { display: inline-block; padding: 10px 16px; border: 1px solid transparent; border-radius: 5px; font-weight: 600; text-decoration: none; cursor: pointer; font-size: 0.95rem; }
        .button-primary { background-color: #4299e1; color: white; border-color: #4299e1; }
        .button-secondary { background-color: #e2e8f0; color: #2d3748; border-color: #cbd5e0; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.95rem; box-sizing: border-box; }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .form-actions { margin-top: 2rem; display: flex; gap: 10px; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-error { color: #9b2c2c; background-color: #fed7d7; }
        .alert-error ul { margin: 0; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Tambah Tiket Baru</h1>
        <p>Buat tiket baru atas nama mahasiswa.</p>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Oops! Ada beberapa masalah:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.tiket.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="pemohon_id">Pemohon (Mahasiswa)</label>
                <select name="pemohon_id" id="pemohon_id">
                    <option value="">-- Pilih Mahasiswa --</option>
                    @foreach($mahasiswas as $mahasiswa)
                        <option value="{{ $mahasiswa->id }}" {{ old('pemohon_id') == $mahasiswa->id ? 'selected' : '' }}>
                            {{ $mahasiswa->name }} ({{ $mahasiswa->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="layanan_id">Layanan</label>
                <select name="layanan_id" id="layanan_id">
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($layanans as $layanan)
                        <option value="{{ $layanan->id }}" {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>
                            {{ $layanan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi Masalah/Permintaan</label>
                <textarea name="deskripsi" id="deskripsi" placeholder="Jelaskan detail masalah atau permintaan...">{{ old('deskripsi') }}</textarea>
            </div>

            <!-- CATATAN: Form untuk detail tiket (seperti keperluan, tahun ajaran, dll)
                 perlu ditambahkan di sini. Anda bisa menggunakan JavaScript
                 untuk menampilkannya secara dinamis berdasarkan pilihan 'Layanan'.
                 Untuk saat ini, form ini akan membuat tiket standar. -->

            <div class="form-actions">
                <button type="submit" class="button button-primary">Simpan Tiket</button>
                <a href="{{ route('admin.tiket.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
