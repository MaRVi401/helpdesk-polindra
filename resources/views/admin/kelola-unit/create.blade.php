<!DOCTYPE html>
<html>
<head>
    <title>Tambah Unit Baru</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8fafc; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input[type="text"], select { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #cbd5e0; box-sizing: border-box; }
        .button { padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; }
        .error-message { color: #e53e3e; font-size: 0.875rem; margin-top: 5px; }
        a { color: #4299e1; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Tambah Unit Baru</h1>
        <form action="{{ route('admin.unit.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama_unit">Nama Unit</label>
                <input type="text" id="nama_unit" name="nama_unit" value="{{ old('nama_unit') }}" required>
                @error('nama_unit')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="kepala_id">Kepala Unit</label>
                <select name="kepala_id" id="kepala_id">
                    <option value="">-- Pilih Kepala Unit --</option>
                    @foreach($staffs as $staff)
                        <option value="{{ $staff->id }}" {{ old('kepala_id') == $staff->id ? 'selected' : '' }}>
                            {{ $staff->user->name }}
                        </option>
                    @endforeach
                </select>
                @error('kepala_id')
                     <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="button">Simpan Unit</button>
            <a href="{{ route('admin.unit.index') }}" style="margin-left: 10px;">Batal</a>
        </form>
    </div>
</body>
</html>