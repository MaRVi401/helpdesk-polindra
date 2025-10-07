<!DOCTYPE html>
<html>
<head>
    <title>Lengkapi Profil</title>
    <style>
        /* Anda bisa menggunakan styling yang sama dari form sebelumnya */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; display: flex; justify-content: center; padding-top: 40px; }
        .form-container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 500px; }
        h3 { margin-top: 0; font-size: 1.5rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 4px; background-color: #4299e1; color: white; font-size: 1rem; font-weight: 600; cursor: pointer; }
        .btn:hover { background-color: #2b6cb0; }
        .errors { background-color: #fed7d7; color: #c53030; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>Lengkapi Profil Mahasiswa Anda</h3>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{-- Sesuaikan dengan URL route Anda --}}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nim">NIM</label>
                <input type="text" name="nim" id="nim" class="form-control" value="{{ old('nim') }}" required>
            </div>

            <div class="form-group">
                <label for="program_studi_id">Program Studi</label>
                <select name="program_studi_id" id="program_studi_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Program Studi --</option>
                    @foreach($programStudi as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->program_studi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tahun_masuk">Tahun Masuk</label>
                <select name="tahun_masuk" id="tahun_masuk" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Tahun --</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ old('tahun_masuk') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn">Simpan Profil</button>
        </form>
    </div>
</body>
</html>