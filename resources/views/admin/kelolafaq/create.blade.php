<!DOCTYPE html>
<html>
<head>
    <title>Tambah FAQ Baru</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 800px; margin: 0 auto; }
        h3 { margin-top: 0; font-size: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #4a5568; }
        input, select, textarea { width: 100%; padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 1rem; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 120px; }
        .button-group { display: flex; gap: 10px; margin-top: 2rem; }
        .button { padding: 10px 16px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .button-primary { background-color: #4299e1; color: white; }
        .button-secondary { background-color: #e2e8f0; color: #4a5568; }
        .button:hover { opacity: 0.9; }
        .is-invalid { border-color: #e53e3e; }
        .invalid-feedback { color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Tambah FAQ Baru</h3>

        <form action="{{ route('admin.kelolafaq.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="judul">Judul</label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" class="@error('judul') is-invalid @enderror" required>
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="layanan_id">Layanan</label>
                <select id="layanan_id" name="layanan_id" class="@error('layanan_id') is-invalid @enderror" required>
                    @foreach($layanans as $layanan)
                        <option value="{{ $layanan->id }}" {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>{{ $layanan->nama }}</option>
                    @endforeach
                </select>
                @error('layanan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="@error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="@error('status') is-invalid @enderror" required>
                    <option value="Draft" {{ old('status', 'Draft') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Post" {{ old('status') == 'Post' ? 'selected' : '' }}>Post</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="button-group">
                <button type="submit" class="button button-primary">Simpan</button>
                <a href="{{ route('admin.kelolafaq.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>

