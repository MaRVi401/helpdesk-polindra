<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen FAQ</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 1200px; margin: 0 auto; }
        h3 { margin-top: 0; font-size: 1.5rem; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 20px; flex-wrap: wrap; }
        .actions-group { display: flex; gap: 10px; align-items: center; }
        .search-form { display: flex; gap: 10px; align-items: center; }
        .search-form input, .search-form select, .button { padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.9rem; }
        .button { background-color: #4299e1; color: white; text-decoration: none; border-radius: 5px; font-weight: 600; border: none; cursor: pointer; }
        .button-green { background-color: #107c41; }
        .button:hover { opacity: 0.9; }
        .button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        thead th { background-color: #f7fafc; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; color: #4a5568; }
        tbody tr:hover { background-color: #f7fafc; }
        .actions a { color: #4299e1; text-decoration: none; font-weight: 600; }
        .actions a:hover { text-decoration: underline; }
        .actions button { background: none; border: none; color: #e53e3e; cursor: pointer; font-weight: 600; padding: 0; margin-left: 10px; font-family: inherit; font-size: inherit; }
        .actions button:hover { text-decoration: underline; }
        .pagination-container { margin-top: 20px; display: flex; justify-content: space-between; align-items: center; }
        .pagination-container nav { display: flex; gap: 5px; }
        .pagination-container .pagination span, .pagination-container .pagination a { padding: 5px 10px; border-radius: 4px; text-decoration: none; color: #4a5568; }
        .pagination-container .pagination .active span { background-color: #4299e1; color: white; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
        .badge-post { background-color: #c6f6d5; color: #2f855a; }
        .badge-draft { background-color: #feebc8; color: #9c4221; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Manajemen Data FAQ</h3>
        
        <div class="toolbar">
            <div class="actions-group">
                <a href="{{ route('admin.kelolafaq.create') }}" class="button">Tambah FAQ</a>
                <button type="button" id="export-button" class="button button-green" disabled>Ekspor Terpilih</button>
            </div>
            
            <form action="{{ route('admin.kelolafaq.index') }}" method="GET" class="search-form" id="search-form">
                <select name="per_page" id="per_page" onchange="this.form.submit()">
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <input type="search" name="q" id="search-input" placeholder="Cari judul..." value="{{ $searchQuery ?? '' }}">
            </form>
        </div>

        @if(session('success'))
            <p class="alert-success">{{ session('success') }}</p>
        @endif

        <form action="{{ route('admin.kelolafaq.export.excel') }}" method="GET" id="export-form" style="display: none;">
            @csrf
        </form>
        <table>
            <thead>
                <tr>
                    <th style="width: 1%;"><input type="checkbox" id="select-all"></th>
                    <th>No.</th>
                    <th>Judul</th>
                    <th>Layanan</th>
                    <th>Status</th>
                    <th>Pembuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr>
                    <td><input type="checkbox" name="selected_faqs[]" class="faq-checkbox" value="{{ $faq->id }}"></td>
                    <td>{{ $faqs->firstItem() + $loop->index }}</td>
                    <td>{{ $faq->judul }}</td>
                    <td>{{ $faq->layanan->nama ?? 'N/A' }}</td>
                    <td>
                        @if($faq->status == 'Post')
                            <span class="badge badge-post">{{ $faq->status }}</span>
                        @else
                            <span class="badge badge-draft">{{ $faq->status }}</span>
                        @endif
                    </td>
                    <td>{{ $faq->user->name ?? 'N/A' }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.kelolafaq.edit', $faq->id) }}">Edit</a>
                        <form action="{{ route('admin.kelolafaq.destroy', $faq->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Anda yakin ingin menghapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        @if ($searchQuery)
                            Tidak ada FAQ yang cocok dengan pencarian "{{ $searchQuery }}".
                        @else
                            Tidak ada data FAQ.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-container">
            <div>
                Menampilkan {{ $faqs->firstItem() ?? 0 }} sampai {{ $faqs->lastItem() ?? 0 }} dari {{ $faqs->total() }} total data
            </div>
            <div>
                {{ $faqs->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all');
            const faqCheckboxes = document.querySelectorAll('.faq-checkbox');
            const exportButton = document.getElementById('export-button');

            function toggleExportButton() {
                const anyChecked = Array.from(faqCheckboxes).some(cb => cb.checked);
                exportButton.disabled = !anyChecked;
            }

            selectAllCheckbox.addEventListener('change', function () {
                faqCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                toggleExportButton();
            });

            faqCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        const allChecked = Array.from(faqCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                    toggleExportButton();
                });
            });

            // FIX: Tambahkan event listener untuk tombol ekspor
            exportButton.addEventListener('click', function() {
                const form = document.getElementById('export-form');
                
                // Bersihkan input lama dari form
                form.innerHTML = '';
                
                // Tambahkan CSRF token kembali
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                // Ambil semua checkbox yang dicentang
                const checkedCheckboxes = document.querySelectorAll('.faq-checkbox:checked');
                
                // Tambahkan setiap ID yang dicentang ke form sebagai input tersembunyi
                checkedCheckboxes.forEach(checkbox => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'selected_faqs[]';
                    hiddenInput.value = checkbox.value;
                    form.appendChild(hiddenInput);
                });
                
                form.submit();
            });

            toggleExportButton();
        });
    </script>
</body>
</html>