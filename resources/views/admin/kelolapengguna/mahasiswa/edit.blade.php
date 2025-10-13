<!DOCTYPE html>
<html>

<head>
    <title>Edit Data Mahasiswa</title>
    <style>
        /* (CSS styling tetap sama seperti sebelumnya, tidak perlu diubah) */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
            line-height: 1.5;
            display: flex;
            justify-content: center;
            padding: 40px;
        }

        .form-container {
            background-color: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }

        h3 {
            margin-top: 0;
            font-size: 1.5rem;
            color: #2d3748;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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

        .form-control:disabled {
            background-color: #edf2f7;
            cursor: not-allowed;
        }

        .btn {
            width: auto;
            padding: 12px 24px;
            border-radius: 4px;
            background-color: #4299e1;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
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
        <h3>Edit Data</h3>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.mahasiswa.update', $mahasiswa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- KIRI --}}
                <div>
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', $mahasiswa->user->name) }}">
                    </div>

                    <div class="form-group">
                        <label for="program_studi_id">Program Studi</label>
                        <select name="program_studi_id" id="program_studi_id" class="form-control">
                            @foreach ($programStudis as $prodi)
                                <option value="{{ $prodi->id }}" @if ($mahasiswa->program_studi_id == $prodi->id) selected @endif>
                                    {{ $prodi->program_studi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', $mahasiswa->user->email) }}">
                    </div>
                </div>

                {{-- KANAN --}}
                <div>
                    <div class="form-group">
                        <label for="nim">NIM</label>
                        <input type="text" name="nim" id="nim" class="form-control"
                            value="{{ old('nim', $mahasiswa->nim) }}">
                    </div>

                    <div class="form-group">
                        <label for="tahun_masuk">Tahun Masuk</label>
                        <select name="tahun_masuk" id="tahun_masuk" class="form-control">
                            @foreach ($years as $year)
                                <option value="{{ $year }}"
                                    {{ old('tahun_masuk', $mahasiswa->tahun_masuk) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="created_at">Akun dibuat pada</label>
                        <input type="text" id="created_at" class="form-control"
                            value="{{ $mahasiswa->user->created_at->format('d F Y') }}" disabled>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>
</body>

</html>
