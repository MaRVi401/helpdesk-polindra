<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kategori Artikel</title>
    {{-- Anda bisa include file CSS utama dari layout Anda --}}
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8fafc; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .button { padding: 8px 12px; border-radius: 5px; text-decoration: none; display: inline-block; font-size: 0.9rem; }
        .button-primary { background-color: #4299e1; color: white; }
        .button-danger { background: none; border: none; color: #e53e3e; cursor: pointer; font-weight: 600; padding: 0; font-family: inherit; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; }
        .alert-error { color: #9b2c2c; background-color: #fed7d7; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kelola Kategori Artikel</h1>
        <a href="{{ route('admin.kategori-artikel.create') }}" class="button button-primary">Tambah Kategori</a>
        <a href="{{ route('admin.artikel.index') }}" class="button" style="background-color: #666; color: white;">Kembali ke Artikel</a>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th>Nama Kategori</th>
                    <th style="width: 25%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kategoris as $kategori)
                <tr>
                    <td>{{ $kategoris->firstItem() + $loop->index }}</td>
                    <td>{{ $kategori->kategori }}</td>
                    <td>
                        <a href="{{ route('admin.kategori-artikel.edit', $kategori->id) }}">Edit</a>
                        <form action="{{ route('admin.kategori-artikel.destroy', $kategori->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align: center;">Belum ada kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
         <div style="margin-top: 20px;">
            {{ $kategoris->links() }}
        </div>
    </div>
</body>
</html>
