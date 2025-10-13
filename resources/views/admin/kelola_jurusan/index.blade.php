<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Jurusan & Program Studi</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .main-container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 1200px; margin: 0 auto; }
        h1, h2 { margin-top: 0; }
        .flex-container { display: flex; gap: 40px; flex-wrap: wrap; }
        .table-wrapper { flex: 1; min-width: 45%; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        thead th { background-color: #f7fafc; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; color: #4a5568; }
        tbody tr:hover { background-color: #f7fafc; }
        .button { display: inline-block; padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 5px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .button-primary { background-color: #4299e1; color: white; border-color: #4299e1; }
        .button-danger { background: none; border: none; color: #e53e3e; cursor: pointer; font-weight: 600; padding: 0; font-family: inherit; font-size: inherit; }
        .button-danger:hover { text-decoration: underline; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; }
        .alert-error { color: #9b2c2c; background-color: #fed7d7; }
        .pagination-container { margin-top: 20px; }
    </style>
</head>
<body>

    <div class="main-container">
        <h1>Manajemen Jurusan & Program Studi</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="flex-container">
            <div class="table-wrapper">
                <h2>Data Jurusan</h2>
                <a href="{{ route('admin.jurusan.create') }}" class="button button-primary">Tambah Jurusan</a>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Jurusan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jurusans as $jurusan)
                        <tr>
                            <td>{{ $loop->iteration + $jurusans->firstItem() - 1 }}</td>
                            <td>{{ $jurusan->nama_jurusan }}</td>
                            <td>
                                <a href="{{ route('admin.jurusan.edit', $jurusan) }}">Edit</a> |
                                <form action="{{ route('admin.jurusan.destroy', $jurusan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus jurusan ini? Ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center;">Tidak ada data jurusan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-container">
                     {{ $jurusans->appends(['prodi_page' => $programStudis->currentPage()])->links() }}
                </div>
            </div>

            <div class="table-wrapper">
                <h2>Data Program Studi</h2>
                <a href="{{ route('admin.program-studi.create') }}" class="button button-primary">Tambah Program Studi</a>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Program Studi</th>
                            <th>Jurusan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programStudis as $prodi)
                        <tr>
                            <td>{{ $loop->iteration + $programStudis->firstItem() - 1 }}</td>
                            <td>{{ $prodi->program_studi }}</td>
                            <td>{{ $prodi->jurusan->nama_jurusan ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.program-studi.edit', $prodi) }}">Edit</a> |
                                <form action="{{ route('admin.program-studi.destroy', $prodi) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus program studi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center;">Tidak ada data program studi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-container">
                    {{ $programStudis->appends(['jurusan_page' => $jurusans->currentPage()])->links() }}
                </div>
            </div>
        </div>
    </div>

</body>
</html>