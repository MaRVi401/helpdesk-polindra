<!DOCTYPE html>
<html>

<head>
    <title>Manajemen Data Staff</title>
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

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        .search-form input,
        .search-form select,
        .search-form button {
            padding: 8px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .search-form button {
            background-color: #4a5568;
            color: white;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        .pagination-container {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Manajemen Data Staff</h3>

        <div class="toolbar">
            {{-- <a href="{{ route('admin.staff.create') }}" class="add-button">Tambah Data</a> --}}

            {{-- Form untuk Search dan Paginate --}}
            <form action="{{ route('admin.staff.index') }}" method="GET" class="search-form">
                <div>
                    <label for="per_page">Tampilkan:</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <input type="search" name="q" placeholder="Pencarian" value="{{ $searchQuery ?? '' }}">
            </form>
        </div>

        @if (session('success'))
            <p class="alert-success">{{ session('success') }}</p>
        @endif

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Unit</th>
                    <th>Jabatan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffs as $staff)
                    <tr>
                        <td>{{ $staffs->firstItem() + $loop->index }}</td>
                        <td>{{ $staff->user->name }}</td>
                        <td>{{ $staff->nik }}</td>
                        <td>{{ $staff->user->email }}</td>
                        <td>{{ $staff->user->role }}</td>
                        <td>{{ $staff->unit->nama_unit ?? 'N/A' }}</td>
                        <td>{{ $staff->jabatan->nama_jabatan ?? 'N/A' }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.staff.edit', $staff->id) }}">Edit</a>
                            <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Anda yakin ingin menghapus data ini?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">
                            @if ($searchQuery)
                                Tidak ada data staff yang cocok dengan pencarian "{{ $searchQuery }}".
                            @else
                                Tidak ada data staff.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-container">
            {{-- Menampilkan informasi jumlah data --}}
            <div>
                Menampilkan {{ $staffs->lastItem() }} dari {{ $staffs->total() }} total data
            </div>
            {{-- Menampilkan link paginasi --}}
            <div>
                {{ $staffs->links() }}
            </div>
        </div>
    </div>

    {{-- Search --}}
    <script>
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search-input');

        searchInput.addEventListener('keyup', () => {
            searchForm.submit();
        });
    </script>
</body>

</html>
