<!DOCTYPE html>
<html>
<head>
    <title>Edit Layanan: {{ $layanan->nama }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .main-container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 800px; margin: 0 auto; }
        h1 { margin-top: 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; }
        .form-group input, .form-group select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.95rem; box-sizing: border-box; }
        .form-actions { margin-top: 2rem; display: flex; gap: 10px; }
        .button { display: inline-block; padding: 10px 16px; border: 1px solid transparent; border-radius: 5px; font-weight: 600; text-decoration: none; cursor: pointer; font-size: 0.95rem; }
        .button-primary { background-color: #4299e1; color: white; border-color: #4299e1; }
        .button-danger { background-color: #f56565; color: white; border-color: #f56565; padding: 6px 12px; }
        .button-secondary { background-color: #e2e8f0; color: #2d3748; border-color: #cbd5e0; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; }
        .alert-error { color: #9b2c2c; background-color: #fed7d7; }

        .pic-list { margin-top: 1rem; padding-left: 0; list-style: none; }
        .pic-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dotted #e2e8f0; }
        .pic-item:last-child { border-bottom: none; }
        .pic-info { font-weight: 500; }
        .pic-actions { margin-left: 10px; }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Edit Layanan: {{ $layanan->nama }}</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Validasi Gagal:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form A: Update Data Dasar Layanan -->
        <form action="{{ route('admin.layanan.update', $layanan) }}" method="POST">
            @csrf
            @method('PUT')

            <h2>Data Dasar Layanan</h2>
            <div class="form-group">
                <label for="nama">Nama Layanan</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $layanan->nama) }}" required>
            </div>
            
            <div class="form-group">
                <label for="unit_id">Unit Penanggung Jawab</label>
                <select name="unit_id" id="unit_id" required>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id', $layanan->unit_id) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->nama_unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="prioritas">Prioritas (0 = Terendah)</label>
                <input type="number" name="prioritas" id="prioritas" value="{{ old('prioritas', $layanan->prioritas) }}" min="0" required>
            </div>

            <div class="form-group">
                <label for="status_arsip">Status Arsip</label>
                <select name="status_arsip" id="status_arsip" required>
                    <option value="0" {{ old('status_arsip', $layanan->status_arsip) == 0 ? 'selected' : '' }}>Tidak Diarsip (Aktif)</option>
                    <option value="1" {{ old('status_arsip', $layanan->status_arsip) == 1 ? 'selected' : '' }}>Diarsip (Non-aktif)</option>
                </select>
            </div>

            <div class="form-actions" style="margin-top: 1rem;">
                <button type="submit" class="button button-primary">Simpan Perubahan Dasar</button>
            </div>
        </form>
        
        <hr style="margin: 2rem 0;">

        <!-- Form B: Kelola Penanggung Jawab (PIC) -->
        <h2>Kelola Penanggung Jawab (PIC)</h2>
        
        <!-- BAGIAN 1: TAMBAH PIC -->
        <h3>Tambah PIC Baru</h3>
        <form action="{{ route('admin.layanan.update', $layanan) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Hidden field untuk memastikan update() hanya memproses PIC -->
            <input type="hidden" name="action" value="add_pic"> 

            <div class="form-group" style="display: flex; gap: 10px;">
                <select name="pic_id_to_add" style="flex-grow: 1;" required>
                    <option value="">-- Pilih Staff untuk PIC --</option>
                    @forelse($availableStaff as $staff)
                        <option value="{{ $staff->id }}">
                            {{ $staff->user->name }} ({{ $staff->nik }})
                        </option>
                    @empty
                        <option value="" disabled>Semua staff sudah menjadi PIC.</option>
                    @endforelse
                </select>
                <button type="submit" class="button button-primary" style="width: 150px;">Tambahkan PIC</button>
            </div>
        </form>

        <!-- BAGIAN 2: DAFTAR PIC AKTIF -->
        <h3 style="margin-top: 2rem;">PIC Aktif ({{ $currentPICS->count() }})</h3>
        
        <ul class="pic-list">
            @forelse($currentPICS as $pic)
                <li class="pic-item">
                    <div class="pic-info">
                        {{ $pic->user->name }} (NIK: {{ $pic->nik }})
                    </div>
                    <div class="pic-actions">
                        <form action="{{ route('admin.layanan.update', $layanan) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <!-- Hidden field untuk menghapus PIC -->
                            <input type="hidden" name="pic_id_to_remove" value="{{ $pic->id }}"> 
                            <button type="submit" class="button button-danger" onclick="return confirm('Anda yakin ingin menghapus PIC ini?')">Hapus</button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="pic-item">Belum ada Penanggung Jawab yang ditetapkan.</li>
            @endforelse
        </ul>

        <div class="form-actions">
            <a href="{{ route('admin.layanan.index') }}" class="button button-secondary">Kembali ke Daftar Layanan</a>
        </div>
    </div>
</body>
</html>