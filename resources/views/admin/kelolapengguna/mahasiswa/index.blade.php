<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Data Mahasiswa</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
            line-height: 1.5;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        h3 {
            margin-top: 0;
            font-size: 1.5rem;
        }
        .add-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
        }
        .add-button:hover {
            background-color: #2b6cb0;
        }
        .alert-success {
            color: #2f855a;
            background-color: #c6f6d5;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        thead th {
            background-color: #f7fafc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #4a5568;
        }
        tbody tr:hover {
            background-color: #f7fafc;
        }
        .actions a {
            color: #4299e1;
            text-decoration: none;
            font-weight: 600;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .actions button {
            background: none;
            border: none;
            color: #e53e3e;
            cursor: pointer;
            font-weight: 600;
            padding: 0;
            margin-left: 10px;
        }
        .actions button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Manajemen Data Mahasiswa</h3>
        {{-- <a href="{{ route('admin.mahasiswa.create') }}" class="add-button">Tambah Data Mahasiswa</a> --}}

        @if(session('success'))
            <p class="alert-success">{{ session('success') }}</p>
        @endif

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lengkap</th>
                    <th>NIM</th>
                    <th>Email</th>
                    <th>Prodi</th>
                    <th>Thn. Masuk</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswas as $mhs)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mhs->user->name }}</td>
                    <td>{{ $mhs->nim }}</td>
                    <td>{{ $mhs->user->email }}</td>
                    <td>{{ $mhs->programStudi->program_studi ?? 'N/A' }}</td>
                    <td>{{ $mhs->tahun_masuk }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.mahasiswa.edit', $mhs->id) }}">Edit</a>
                        <form action="{{ route('admin.mahasiswa.destroy', $mhs->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Anda yakin ingin menghapus data ini?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data mahasiswa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $mahasiswas->links() }}
        </div>
    </div>
</body>
</html>