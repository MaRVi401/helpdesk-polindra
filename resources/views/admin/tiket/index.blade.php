<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Tiket</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .main-container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 1200px; margin: 0 auto; }
        h1 { margin-top: 0; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 20px; flex-wrap: wrap; }
        .actions-group { display: flex; gap: 10px; align-items: center; }
        .search-form { display: flex; gap: 10px; align-items: center; }
        .search-form input, .search-form select, .button { padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        thead th { background-color: #f7fafc; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; color: #4a5568; }
        tbody tr:hover { background-color: #f7fafc; }
        
        /* Modifikasi untuk tombol ikon */
        .button { 
            display: inline-block; 
            padding: 8px 12px; 
            border: 1px solid transparent; 
            border-radius: 5px; 
            font-weight: 600; 
            text-decoration: none; 
            cursor: pointer; 
        }
        .button-icon {
            padding: 6px 8px !important; /* Padding lebih kecil untuk ikon */
            width: 32px; 
            height: 32px;
            text-align: center;
            line-height: 1; /* Pastikan ikon terpusat */
        }
        .button-primary { background-color: #4299e1; color: white; border-color: #4299e1; }
        .button-danger { background-color: #f56565; color: white; border-color: #f56565; }
        .button-green { background-color: #107c41; color: white; border-color: #107c41; }
        .button:disabled { background-color: #cccccc; cursor: not-allowed; border-color: #cccccc; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; }
        .alert-error { color: #9b2c2c; background-color: #fed7d7; }
        .pagination-container { margin-top: 20px; }
        /* Pastikan elemen aksi sejajar */
        .action-buttons a, .action-buttons form { display: inline-block; margin-right: 5px; vertical-align: middle; }
        
        /* --- CSS BARU UNTUK STATUS DETAIL --- */
        /* Hapus CSS status lama (status-pending, status-diproses, dll.) */
        
        .status-badge { 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            color: white;
            text-transform: capitalize; 
        }

        /* Abu-abu: Diajukan oleh Pemohon */
        .status-diajukan_oleh_pemohon { 
            background-color: #a0aec0; /* Abu-abu */
            color: #2d3748; /* Text gelap agar terlihat di abu-abu terang */
        } 
        /* Kuning: Ditangani/Diselesaikan oleh PIC */
        .status-ditangani_oleh_pic, .status-diselesaikan_oleh_pic { 
            background-color: #f6ad55; /* Kuning */
            color: white;
        }
        /* Merah: Belum Selesai/Bermasalah */
        .status-dinilai_belum_selesai_oleh_pemohon, .status-pemohon_bermasalah { 
            background-color: #f56565; /* Merah */
            color: white;
        }
        /* Hijau: Selesai oleh Kepala/Pemohon */
        .status-dinilai_selesai_oleh_kepala, .status-dinilai_selesai_oleh_pemohon { 
            background-color: #48bb78; /* Hijau */
            color: white;
        }
        /* --- AKHIR CSS BARU --- */
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Manajemen Tiket</h1>

        <div class="toolbar">
            <div class="actions-group">
                <a href="{{ route('admin.tiket.create') }}" class="button button-primary">Tambah Tiket</a> 
                <button type="button" id="export-btn" class="button button-green" disabled>Ekspor Terpilih</button>
            </div>
            <form id="search-form" action="{{ route('admin.tiket.index') }}" method="GET" class="search-form">
                <select name="per_page" id="per-page-select" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
                <select name="status" id="status-filter" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $statusFilter == $status ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', $status) }}
                        </option>
                    @endforeach
                </select>
                <input type="search" id="search-input" name="q" placeholder="Cari (No. Tiket, Pemohon, Layanan...)" value="{{ $searchQuery ?? '' }}" style="width: 250px;">
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
                    <th>No. Tiket</th>
                    <th>Layanan</th>
                    <th>Pemohon</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tikets as $tiket)
                <tr>
                    <td><input type="checkbox" class="row-checkbox" value="{{ $tiket->id }}"></td>
                    <td>{{ $loop->iteration + $tikets->firstItem() - 1 }}</td>
                    <td>{{ $tiket->no_tiket }}</td>
                    <td>{{ $tiket->layanan->nama ?? 'N/A' }}</td>
                    <td>{{ $tiket->pemohon->name ?? 'N/A' }}</td>
                    <td>{{ $tiket->layanan->unit->nama_unit ?? 'N/A' }}</td>
                    <td>
                        @php
                            // Ambil nilai ENUM status terbaru (Default jika null)
                            $status = $tiket->statusTerbaru->status ?? 'Diajukan_oleh_Pemohon';
                            // Konversi status ENUM menjadi kelas CSS
                            $statusClass = 'status-' . strtolower($status);
                            // Konversi status ENUM menjadi label yang mudah dibaca
                            $statusLabel = str_replace('_', ' ', $status);
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="action-buttons">
                        
                        {{-- 1. Detail / Balas (Icon: Edit) --}}
                        <a href="{{ route('admin.tiket.edit', $tiket) }}" class="button button-icon button-primary" title="Detail / Balas">
                            <i class="ti ti-edit"></i>
                        </a>

                        {{-- 2. Hapus (Icon: Trash) --}}
                        <form action="{{ route('admin.tiket.destroy', $tiket->id) }}" method="POST" 
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus Tiket #{{ $tiket->no_tiket }} secara permanen? Aksi ini tidak dapat dibatalkan.');" 
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button button-icon button-danger" title="Hapus">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data tiket.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $tikets->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIKA PENCARIAN OTOMATIS (DEBOUNCE) ---
            const searchInput = document.getElementById('search-input');
            const searchForm = document.getElementById('search-form');
            let debounceTimer;

            searchInput.addEventListener('keyup', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    searchForm.submit();
                }, 300); // Tunda 300ms
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
                    const baseUrl = '{{ route("admin.tiket.export.excel") }}';
                    const queryString = selectedIds.map(id => `selected_ids[]=${id}`).join('&');
                    window.location.href = `${baseUrl}?${queryString}`;
                }
            });

            toggleExportButton();
        });
    </script>
</body>
</html>