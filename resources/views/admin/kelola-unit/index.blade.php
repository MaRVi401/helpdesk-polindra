<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Unit</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .main-container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 900px; margin: 0 auto; }
        h1 { margin-top: 0; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 20px; flex-wrap: wrap; }
        .search-form { display: flex; gap: 10px; align-items: center; }
        .search-form input, .search-form select, .button { padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        thead th { background-color: #f7fafc; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; color: #4a5568; }
        tbody tr:hover { background-color: #f7fafc; }
        .button { display: inline-block; padding: 8px 12px; border: 1px solid transparent; border-radius: 5px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .button-primary { background-color: #4299e1; color: white; border-color: #4299e1; }
        .button-danger { background: none; border: none; color: #e53e3e; cursor: pointer; font-weight: 600; padding: 0; font-family: inherit; font-size: inherit; }
        .button-danger:hover { text-decoration: underline; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; }
        .alert-error { color: #9b2c2c; background-color: #fed7d7; }
        .pagination-container { margin-top: 20px; }
        .action-buttons a, .action-buttons form { display: inline-block; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Manajemen Unit</h1>

        <div class="toolbar">
            <a href="{{ route('admin.unit.create') }}" class="button button-primary">Tambah Unit</a>
            <form id="search-form" action="{{ route('admin.unit.index') }}" method="GET" class="search-form">
                <select name="per_page" onchange="this.form.submit()">
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                </select>
                <input type="search" id="search-input" name="q" placeholder="Cari unit..." value="{{ $searchQuery ?? '' }}">
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Unit</th>
                    <th>Kepala Unit</th>
                    <th style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                <tr>
                    <td>{{ $loop->iteration + $units->firstItem() - 1 }}</td>
                    <td>{{ $unit->nama_unit }}</td>
                    <td>{{ $unit->kepalaUnit->user->name ?? 'Belum Ditentukan' }}</td>
                    <td class="action-buttons">
                        <a href="{{ route('admin.unit.edit', $unit) }}">Edit</a>
                        <form action="{{ route('admin.unit.destroy', $unit) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus unit ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data unit.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $units->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const searchForm = document.getElementById('search-form');
            let debounceTimer;

            searchInput.addEventListener('keyup', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    searchForm.submit();
                }, 300);
            });
        });
    </script>
</body>
</html>