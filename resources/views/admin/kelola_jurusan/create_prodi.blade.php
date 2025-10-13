<!DOCTYPE html>
<html>
<head>
    <title>Tambah Program Studi Baru</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8fafc; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input, select { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #cbd5e0; box-sizing: border-box; }
        .button { padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; }
        .error-message { color: #e53e3e; font-size: 0.875rem; margin-top: 5px; }
        a { color: #4299e1; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Tambah Program Studi Baru</h1>
        <form action="{{ route('admin.program-studi.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="jurusan_id">Pilih Jurusan</label>
                <select name="jurusan_id" id="jurusan_id" required>
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                            {{ $jurusan->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
                @error('jurusan_id')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="program_studi">Nama Program Studi</label>
                <input type="text" id="program_studi" name="program_studi" value="{{ old('program_studi') }}" required>
                @error('program_studi')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="button">Simpan Program Studi</button>
            <a href="{{ route('admin.program-studi.index') }}" style="margin-left: 10px;">Batal</a>
        </form>
    </div>
</body>
</html>