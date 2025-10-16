<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Artikel</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .main-container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 1200px; margin: 0 auto; }
        h1 { margin-top: 0; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 20px; flex-wrap: wrap; }
        .actions-group { display: flex; gap: 10px; }
        .filters { display: flex; gap: 10px; align-items: center; }
        .filters input, .filters select, .button { padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        thead th { background-color: #f7fafc; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; color: #4a5568; }
        .button { display: inline-block; border: 1px solid transparent; border-radius: 5px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .button-primary { background-color: #4299e1; color: white; }
        .button-secondary { background-color: #63b3ed; color: white; }
        .button-danger { background: none; border: none; color: #e53e3e; cursor: pointer; font-weight: 600; padding: 0; font-family: inherit; font-size: inherit; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; }
        .pagination-container { margin-top: 20px; }
        .thumbnail { width: 80px; height: 50px; object-fit: cover; border-radius: 4px; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
        .badge-post { background-color: #c6f6d5; color: #2f855a; }
        .badge-draft { background-color: #feebc8; color: #9c4221; }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Manajemen Artikel</h1>

        <div class="toolbar">
            <div class="actions-group">
                <a href="{{ route('admin.artikel.create') }}" class="button button-primary">Tambah Artikel</a>
                <a href="{{ route('admin.kategori-artikel.index') }}" class="button button-secondary">Kelola Kategori</a>
            </div>
            <form action="{{ route('admin.artikel.index') }}" method="GET" class="filters">
                <select name="kategori" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->kategori }}
                        </option>
                    @endforeach
                </select>
                <input type="search" name="q" placeholder="Cari judul..." value="{{ request('q') }}">
                <button type="submit" class="button">Cari</button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 100px;">Gambar</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Penulis</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($artikels as $artikel)
                <tr>
                    <td>
                        @if($artikel->gambar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($artikel->gambar) }}" alt="Gambar saat ini" class="current-image">
                        @else
                            <div style="width: 80px; height: 50px; background-color: #e2e8f0; border-radius: 4px; display:flex; align-items:center; justify-content:center; font-size:10px; color: #718096;">No Image</div>
                        @endif
                    </td>
                    <td>{{ Str::limit($artikel->judul, 45) }}</td>
                    <td>{{ $artikel->kategori->kategori ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($artikel->status) }}">{{ $artikel->status }}</span>
                    </td>
                    <td>{{ $artikel->user->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('admin.artikel.edit', $artikel) }}">Edit</a> |
                        <form action="{{ route('admin.artikel.destroy', $artikel) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada artikel ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $artikels->links() }}
        </div>
    </div>
</body>
</html>
