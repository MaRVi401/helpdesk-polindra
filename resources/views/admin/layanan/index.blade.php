<!DOCTYPE html>
<html>

<head>
    <title>Manajemen Layanan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
            line-height: 1.5;
            padding: 20px;
        }

        .main-container {
            background-color: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        h1 {
            margin-top: 0;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .actions-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-form input,
        .search-form select,
        .button {
            padding: 8px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
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

        .button {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid transparent;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            background-color: #4299e1;
            color: white;
            border-color: #4299e1;
        }

        .button-danger {
            background: none;
            border: none;
            color: #e53e3e;
            cursor: pointer;
            font-weight: 600;
            padding: 0;
            font-family: inherit;
            font-size: inherit;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }

        .alert-success {
            color: #2f855a;
            background-color: #c6f6d5;
        }

        .alert-error {
            color: #9b2c2c;
            background-color: #fed7d7;
        }

        .pagination-container {
            margin-top: 20px;
        }

        .action-buttons a,
        .action-buttons form {
            display: inline-block;
            margin-right: 10px;
        }

        .pic-list {
            margin: 0;
            padding-left: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <h1>Manajemen Layanan & PIC</h1>

        <div class="toolbar">
            <div class="actions-group">
                <a href="{{ route('admin.layanan.create') }}" class="button button-primary">Tambah Layanan</a>
            </div>

            {{-- Filter Unit --}}
            <form id="filter-form" action="{{ route('admin.layanan.index') }}" method="GET" class="search-form"
                style="gap: 10px;">
                <select name="unit_id" onchange="this.form.submit()" class="button">
                    <option value="">Semua Unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" {{ $filterUnitId == $unit->id ? 'selected' : '' }}>
                            {{ $unit->nama_unit }}
                        </option>
                    @endforeach
                </select>

                <select name="per_page" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
                <input type="search" id="search-input" name="q" placeholder="Cari layanan, unit, atau PIC..."
                    value="{{ $searchQuery ?? '' }}">
            </form>
            {{-- Akhir dari filter Unit --}}
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th>Nama Layanan</th>
                    <th>Unit</th>
                    <th>PIC (Penanggung Jawab)</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($layanans as $layanan)
                    <tr>
                        <td>{{ $loop->iteration + $layanans->firstItem() - 1 }}</td>
                        <td>
                            {{ $layanan->nama }}
                            @if ($layanan->status_arsip)
                                <span style="color: #e53e3e; font-size: 0.8rem;">(Diarsip)</span>
                            @endif
                        </td>
                        <td>{{ $layanan->unit->nama_unit ?? 'N/A' }}</td>
                        <td>
                            {{-- Menggunakan relasi 'penanggungJawab' --}}
                            @if ($layanan->penanggungJawab->isEmpty())
                                <span style="color: #718096;">Belum diatur</span>
                            @else
                                <ul class="pic-list">
                                    @foreach ($layanan->penanggungJawab as $pic)
                                        <li>{{ $pic->user->name ?? 'N/A' }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td class="action-buttons">
                            <a href="{{ route('admin.layanan.edit', $layanan) }}">Edit</a>
                            <form action="{{ route('admin.layanan.destroy', $layanan) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Yakin ingin menghapus layanan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Tidak ada data layanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $layanans->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const filterForm = document.getElementById('filter-form'); // Mengubah ke filter-form
            let debounceTimer;

            searchInput.addEventListener('keyup', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    // Pastikan nilai unit_id yang sudah dipilih tetap terkirim saat pencarian di-trigger
                    // Karena kita menggunakan satu form, ini sudah otomatis terhandle.
                    filterForm.submit();
                }, 300);
            });
        });
    </script>
</body>

</html>
