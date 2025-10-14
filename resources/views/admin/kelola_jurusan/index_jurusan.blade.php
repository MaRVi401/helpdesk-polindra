<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Jurusan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .main-container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 900px; margin: 0 auto; }
        h1 { margin-top: 0; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 20px; flex-wrap: wrap; }
        .actions-group { display: flex; gap: 10px; align-items: center; }
        .search-form { display: flex; gap: 10px; align-items: center; }
        .search-form input, .search-form select, .button { padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        thead th { background-color: #f7fafc; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; color: #4a5568; }
        tbody tr:hover { background-color: #f7fafc; }
        .button { display: inline-block; padding: 8px 12px; border: 1px solid transparent; border-radius: 5px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .button-primary { background-color: #4299e1; color: white; border-color: #4299e1; }
        .button-secondary { background-color: #6c757d; color: white; border-color: #6c757d;}
        .button-green { background-color: #107c41; color: white; border-color: #107c41; }
        .button-danger { background: none; border: none; color: #e53e3e; cursor: pointer; font-weight: 600; padding: 0; font-family: inherit; font-size: inherit; }
        .button-danger:hover { text-decoration: underline; }
        .button:disabled { background-color: #cccccc; cursor: not-allowed; border-color: #cccccc; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; }
        .alert-error { color: #9b2c2c; background-color: #fed7d7; }
        .pagination-container { margin-top: 20px; }
        .action-buttons a, .action-buttons form { display: inline-block; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Manajemen Jurusan</h1>

        <div class="toolbar">
            <div class="actions-group">
                <a href="{{ route('admin.jurusan.create') }}" class="button button-primary">Tambah Jurusan</a>
                <button type="button" id="export-btn" class="button button-green" disabled>Ekspor Terpilih</button>
            </div>
            <form id="search-form" action="{{ route('admin.jurusan.index') }}" method="GET" class="search-form">
                <select name="per_page" onchange="this.form.submit()">
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                </select>
                <input type="search" id="search-input" name="q" placeholder="Cari jurusan..." value="{{ $searchQuery ?? '' }}">
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
                    <th style="width: 1%;"><input type="checkbox" id="select-all"></th>
                    <th>No</th>
                    <th>Nama Jurusan</th>
                    <th>Total Prodi</th>
                    <th style="width: 25%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jurusans as $jurusan)
                <tr>
                    <td><input type="checkbox" class="row-checkbox" value="{{ $jurusan->id }}"></td>
                    <td>{{ $loop->iteration + $jurusans->firstItem() - 1 }}</td>
                    <td>{{ $jurusan->nama_jurusan }}</td>
                    <td>{{ $jurusan->program_studis_count }}</td>
                    <td class="action-buttons">
                        <a href="{{ route('admin.jurusan.program-studi.index', $jurusan) }}" class="button button-secondary">View Prodi</a>
                        <a href="{{ route('admin.jurusan.edit', $jurusan) }}">Edit</a>
                        <form action="{{ route('admin.jurusan.destroy', $jurusan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus jurusan ini? Ini akan menghapus semua prodi di dalamnya.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        @if ($searchQuery)
                            Tidak ada Jurusan yang cocok dengan pencarian "{{ $searchQuery }}".
                        @else
                            Tidak ada data Jurusan.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-container">
            <div>
                Menampilkan {{ $jurusans->firstItem() ?? 0 }} sampai {{ $jurusans->lastItem() ?? 0 }} dari {{ $jurusans->total() }} total data
            </div>
            <div>
                {{ $jurusans->links() }}
            </div>
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

            // --- LOGIKA EKSPOR DATA ---
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const exportBtn = document.getElementById('export-btn');

            function toggleExportButton() {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                exportBtn.disabled = !anyChecked;
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleExportButton();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAll.checked = false;
                    } else {
                        const allChecked = Array.from(checkboxes).every(c => c.checked);
                        selectAll.checked = allChecked;
                    }
                    toggleExportButton();
                });
            });
            
            exportBtn.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                                       .map(cb => cb.value);
                
                if (selectedIds.length > 0) {
                    const baseUrl = '{{ route("admin.jurusan.export.excel") }}';
                    const queryString = selectedIds.map(id => `selected_ids[]=${id}`).join('&');
                    window.location.href = `${baseUrl}?${queryString}`;
                }
            });

            toggleExportButton(); // Cek status tombol saat halaman dimuat
        });
    </script>
</body>
</html>

