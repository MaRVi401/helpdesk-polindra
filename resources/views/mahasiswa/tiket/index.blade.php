<!DOCTYPE html>
<html>
<head>
    <title>Daftar Tiket Saya</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8fafc; color: #2d3748; line-height: 1.5; padding: 20px; }
        .container { background-color: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 1200px; margin: 0 auto; }
        h3 { margin-top: 0; font-size: 1.5rem; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .button { padding: 8px 12px; background-color: #4299e1; color: white; text-decoration: none; border-radius: 5px; font-weight: 600; border: none; cursor: pointer; }
        .button-info { background-color: #63b3ed; }
        .button:hover { opacity: 0.9; }
        .alert-success { color: #2f855a; background-color: #c6f6d5; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        thead th { background-color: #f7fafc; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; color: #4a5568; }
        tbody tr:hover { background-color: #f7fafc; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
        .badge-warning { background-color: #feebc8; color: #9c4221; }
        .badge-info { background-color: #bee3f8; color: #2a4365; }
        .badge-success { background-color: #c6f6d5; color: #2f855a; }
        .badge-danger { background-color: #fed7d7; color: #9b2c2c; }
        .badge-secondary { background-color: #e2e8f0; color: #4a5568; }
    </style>
</head>
<body>
    <div class="container">
        <div class="toolbar">
            <h3>Daftar Tiket Saya</h3>
            <a href="{{ route('mahasiswa.tiket.create') }}" class="button">Buat Tiket Baru</a>
        </div>

        @if(session('success'))
            <p class="alert-success">{{ session('success') }}</p>
        @endif

        <table>
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Judul</th>
                    <th>Layanan</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tikets as $tiket)
                <tr>
                    <td><a href="{{ route('mahasiswa.tiket.show', $tiket->id) }}"><strong>{{ $tiket->no_tiket }}</strong></a></td>
                    <td>{{ Str::limit($tiket->judul, 35) }}</td>
                    {{-- DISESUAIKAN: Menggunakan 'nama_layanan' dan menambahkan pengecekan jika relasi null --}}
                    <td>{{ $tiket->layanan->nama_layanan ?? '[Layanan Dihapus]' }}</td>
                    <td>
                        @if($tiket->status == 'Menunggu') <span class="badge badge-warning">{{ $tiket->status }}</span>
                        @elseif($tiket->status == 'Diproses') <span class="badge badge-info">{{ $tiket->status }}</span>
                        @elseif($tiket->status == 'Selesai') <span class="badge badge-success">{{ $tiket->status }}</span>
                        @elseif($tiket->status == 'Ditolak') <span class="badge badge-danger">{{ $tiket->status }}</span>
                        @else <span class="badge badge-secondary">{{ $tiket->status }}</span> @endif
                    </td>
                    <td>{{ $tiket->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <a href="{{ route('mahasiswa.tiket.show', $tiket->id) }}" class="button button-info">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Anda belum memiliki tiket layanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $tikets->links() }}</div>
        <a href="{{ route('mahasiswa.dashboard') }}" style="margin-top: 20px; display:inline-block;">Kembali ke Dashboard</a>
    </div>
</body>
</html>
