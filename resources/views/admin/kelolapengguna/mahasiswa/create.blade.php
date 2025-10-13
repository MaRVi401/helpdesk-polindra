{{-- <!DOCTYPE html>
<html>
<head>
    <title>Tambah Data Mahasiswa</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
            line-height: 1.5;
            display: flex;
            justify-content: center;
            padding-top: 40px;
        }
        .form-container {
            background-color: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h3 {
            margin-top: 0;
            font-size: 1.5rem;
            color: #2d3748;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            background-color: #4299e1;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn:hover {
            background-color: #2b6cb0;
        }
        .errors {
            background-color: #fed7d7;
            color: #c53030;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        .errors li {
            list-style-position: inside;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>Tambah Data Mahasiswa Baru</h3>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="user_id">User</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="program_studi_id">Program Studi</label>
                <select name="program_studi_id" id="program_studi_id" class="form-control">
                    <option value="">-- Pilih Program Studi --</option>
                    @foreach($programStudis as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nim">NIM</label>
                <input type="text" name="nim" id="nim" class="form-control" value="{{ old('nim') }}">
            </div>

            <div class="form-group">
                <label for="tahun_masuk">Tahun Masuk</label>
                <input type="text" name="tahun_masuk" id="tahun_masuk" class="form-control" value="{{ old('tahun_masuk') }}">
            </div>

            <button type="submit" class="btn">Simpan Data</button>
        </form>
    </div>
</body>
</html> --}}