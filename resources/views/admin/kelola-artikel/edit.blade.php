<!DOCTYPE html>
<html>
<head>
    <title>Edit Artikel</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8fafc; }
        .form-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input[type="text"], select, textarea, input[type="file"] { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #cbd5e0; box-sizing: border-box; }
        textarea { min-height: 200px; }
        .button { padding: 10px 20px; background-color: #48bb78; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; }
        .error-message { color: #e53e3e; font-size: 0.875rem; margin-top: 5px; }
        a { color: #4299e1; text-decoration: none;}
        .row { display: flex; gap: 20px; }
        .col { flex: 1; }
        .current-image { max-width: 200px; margin-top: 10px; display: block; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Artikel</h1>
        <form action="{{ route('admin.artikel.update', $artikel->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="judul">Judul</label>
                <input type="text" id="judul" name="judul" value="{{ old('judul', $artikel->judul) }}" required>
                @error('judul') <p class="error-message">{{ $message }}</p> @enderror
            </div>

            <div class="row">
                <div class="col form-group">
                    <label for="kategori_id">Kategori</label>
                    <select name="kategori_id" id="kategori_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $artikel->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id') <p class="error-message">{{ $message }}</p> @enderror
                </div>
                <div class="col form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" required>
                        <option value="Draft" {{ old('status', $artikel->status) == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Post" {{ old('status', $artikel->status) == 'Post' ? 'selected' : '' }}>Post</option>
                    </select>
                    @error('status') <p class="error-message">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Isi Konten</label>
                <textarea id="deskripsi" name="deskripsi" required>{{ old('deskripsi', $artikel->deskripsi) }}</textarea>
                @error('deskripsi') <p class="error-message">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="gambar">Ganti Gambar (Opsional)</label>
                @if($artikel->gambar)
                    <p>Gambar saat ini:</p>
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($artikel->gambar) }}" alt="Gambar saat ini" class="current-image">
                @endif
                <input type="file" id="gambar" name="gambar" accept="image/*" style="margin-top: 10px;">
                @error('gambar') <p class="error-message">{{ $message }}</p> @enderror
            </div>
            
            <button type="submit" class="button">Update Artikel</button>
            <a href="{{ route('admin.artikel.index') }}" style="margin-left: 10px;">Batal</a>
        </form>
    </div>
</body>
</html>
