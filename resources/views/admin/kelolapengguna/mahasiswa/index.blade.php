<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Data Mahasiswa</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 1200px; margin: 0 auto; }
        h3 { margin-top: 0; font-size: 1.5rem; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 20px; }
        .toolbar .actions-group { display: flex; gap: 10px; align-items: center; }
        .search-form { display: flex; gap: 10px; }
        .search-form input, .search-form select { padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.9rem; }
        .export-button { padding: 8px 12px; background-color: #107c41; color: white; text-decoration: none; border-radius: 5px; font-weight: 600; border: none; cursor: pointer; font-size: 0.9rem; }
        .export-button:hover { background-color: #0a6832; }
        .export-button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        thead th { background-color: #f7fafc; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; color: #4a5568; }
        tbody tr:hover { background-color: #f7fafc; }
        .actions a { color: #4299e1; text-decoration: none; font-weight: 600; }
        .actions a:hover { text-decoration: underline; }
        .actions button { background: none; border: none; color: #e53e3e; cursor: pointer; font-weight: 600; padding: 0; margin-left: 10px; }
        .actions button:hover { text-decoration: underline; }
        .pagination-container { margin-top: 20px; display: flex; justify-content: space-between; align-items: center; }
        .pagination-container nav svg { width: 1.25rem; height: 1.25rem; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Manajemen Data Mahasiswa</h3>
        
        <div class="toolbar">
            <div class="actions-group">
                {{-- <a href="{{ route('admin.mahasiswa.create') }}" class="add-button">Tambah Data</a> --}}
                <button type="submit" form="export-form" id="export-button" class="export-button" disabled>Ekspor Terpilih</button>
            </div>
            
            <form action="{{ route('admin.mahasiswa.index') }}" method="GET" class="search-form" id="search-form">
                <div>
                    <label for="per_page">Tampilkan:</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <input type="search" name="q" id="search-input" placeholder="Pencarian..." value="{{ $searchQuery ?? '' }}">
            </form>
        </div>

        @if(session('success'))
            <p class="alert-success">{{ session('success') }}</p>
        @endif

        <form action="{{ route('admin.mahasiswa.export.excel') }}" method="GET" id="export-form">
            <table>
                <thead>
                    <tr>
                        <th style="width: 1%;"><input type="checkbox" id="select-all"></th>
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
                        <td><input type="checkbox" name="selected_mahasiswa[]" class="mahasiswa-checkbox" value="{{ $mhs->id }}"></td>
                        <td>{{ $mahasiswas->firstItem() + $loop->index }}</td>
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
                        <td colspan="8" style="text-align: center; padding: 20px;">
                            @if ($searchQuery)
                                Tidak ada data mahasiswa yang cocok dengan pencarian "{{ $searchQuery }}".
                            @else
                                Tidak ada data mahasiswa.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>

        <div class="pagination-container">
            <div>
                Menampilkan {{ $mahasiswas->firstItem() ?? 0 }} sampai {{ $mahasiswas->lastItem() ?? 0 }} dari {{ $mahasiswas->total() }} total data
            </div>
            <div>
                {{ $mahasiswas->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Logika untuk Pencarian Otomatis
            const searchForm = document.getElementById('search-form');
            const searchInput = document.getElementById('search-input');
            const baseUrl = "{{ route('admin.mahasiswa.index') }}";
            let debounceTimer;

            searchInput.addEventListener('keyup', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const perPageSelect = document.getElementById('per_page');
                    const perPageValue = perPageSelect.value;
                    if (searchInput.value.trim() === '') {
                        window.location.href = baseUrl + '?per_page=' + perPageValue;
                    } else {
                        searchForm.submit();
                    }
                }, 300);
            });

            // Logika untuk Checkbox Ekspor
            const selectAllCheckbox = document.getElementById('select-all');
            const mahasiswaCheckboxes = document.querySelectorAll('.mahasiswa-checkbox');
            const exportButton = document.getElementById('export-button');

            function toggleExportButton() {
                const anyChecked = Array.from(mahasiswaCheckboxes).some(cb => cb.checked);
                exportButton.disabled = !anyChecked;
            }

            selectAllCheckbox.addEventListener('change', function () {
                mahasiswaCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                toggleExportButton();
            });

            mahasiswaCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        const allChecked = Array.from(mahasiswaCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                    toggleExportButton();
                });
            });

            toggleExportButton();
        });
    </script>
</body>
</html>