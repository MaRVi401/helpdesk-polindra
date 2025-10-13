<!DOCTYPE html>
<html>

<head>
    <title>Edit Data Staff</title>
    <style>
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

        <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- KIRI --}}
                <div>
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', $staff->user->name) }}">
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', $staff->user->email) }}">
                    </div>

                    <div class="form-group">
                        <label for="nik">NIK</label>
                        <input type="text" name="nik" id="nik" class="form-control"
                            value="{{ old('nik', $staff->nik) }}">
                    </div>
                </div>

                {{-- KANAN --}}
                <div>
                    <div class="form-group">
                        <label for="unit_id">Unit</label>
                        <select name="unit_id" id="unit_id" class="form-control">
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @if ($staff->unit_id == $unit->id) selected @endif>
                                    {{ $unit->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jabatan_id">Jabatan</label>
                        <select name="jabatan_id" id="jabatan_id" class="form-control">
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}" @if ($staff->jabatan_id == $jabatan->id) selected @endif>
                                    {{ $jabatan->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="role">Role Pengguna</label>
                        <select name="role" id="role" class="form-control">
                            <option value="super_admin" @if ($staff->user->role == 'super_admin') selected @endif>Super Admin
                            </option>
                            <option value="kepala_unit" @if ($staff->user->role == 'kepala_unit') selected @endif>Kepala Unit
                            </option>
                            <option value="admin_unit" @if ($staff->user->role == 'admin_unit') selected @endif>Admin Unit
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="created_at">Akun dibuat pada</label>
                        <input type="text" id="created_at" class="form-control"
                            value="{{ $staff->user->created_at->format('d F Y') }}" disabled>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>
</body>

</html>
