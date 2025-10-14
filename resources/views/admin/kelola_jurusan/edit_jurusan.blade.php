<!DOCTYPE html>
<html>
<head>
    <title>Edit Jurusan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8fafc; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input[type="text"] { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #cbd5e0; box-sizing: border-box; }
        .button { padding: 10px 20px; background-color: #48bb78; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; }
        .error-message { color: #e53e3e; font-size: 0.875rem; margin-top: 5px; }
        a { color: #4299e1; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Jurusan</h1>
        <form action="{{ route('admin.jurusan.update', $jurusan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nama_jurusan">Nama Jurusan</label>
                <input type="text" id="nama_jurusan" name="nama_jurusan" value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}" required>
                @error('nama_jurusan')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="button">Update Jurusan</button>
            <a href="{{ route('admin.jurusan.index') }}" style="margin-left: 10px;">Batal</a>
        </form>
    </div>
</body>
</html>