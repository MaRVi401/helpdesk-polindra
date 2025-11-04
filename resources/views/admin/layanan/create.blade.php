<!DOCTYPE html>
<html>
<head>
    <title>Tambah Layanan Baru</title>
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
        .form-group select[multiple] { height: 160px; }
        .form-group-check { display: flex; align-items: center; gap: 10px; }
        .form-actions { margin-top: 2rem; display: flex; gap: 10px; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-error { color: #9b2c2c; background-color: #fed7d7; }
        .alert-error ul { margin: 0; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Tambah Layanan Baru</h1>

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

        <form action="{{ route('admin.layanan.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="nama">Nama Layanan</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required>
            </div>

            <div class="form-group">
                <label for="unit_id">Unit Penanggung Jawab</label>
                <select name="unit_id" id="unit_id" required>
                    <option value="">-- Pilih Unit --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->nama_unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="prioritas">Prioritas (Opsional)</label>
                <input type="number" name="prioritas" id="prioritas" value="{{ old('prioritas', 0) }}" min="0">
            </div>

            <div class="form-group">
                <label for="penanggung_jawab_ids">Tetapkan PIC (Penanggung Jawab)</label>
                <select name="penanggung_jawab_ids[]" id="penanggung_jawab_ids" multiple size="8">
                    @foreach($allStaff as $staff)
                        <option value="{{ $staff->id }}" {{ in_array($staff->id, old('penanggung_jawab_ids', [])) ? 'selected' : '' }}>
                            {{ $staff->user->name ?? 'N/A' }} (Unit: {{ $staff->unit->nama_unit ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                <small>Tahan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu.</small>
            </div>
            
            <div class="form-group form-group-check">
                <input type="checkbox" name="status_arsip" id="status_arsip" value="1" {{ old('status_arsip') ? 'checked' : '' }}>
                <label for="status_arsip" style="margin-bottom: 0;">Arsipkan layanan ini (sembunyikan dari mahasiswa)</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Simpan Layanan</button>
                <a href="{{ route('admin.layanan.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>

