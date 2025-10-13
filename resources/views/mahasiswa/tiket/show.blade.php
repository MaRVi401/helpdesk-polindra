<!DOCTYPE html>
<html>
<head>
    <title>Detail Tiket: {{ $tiket->no_tiket }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f0f2f5; color: #050505; line-height: 1.5; padding: 20px; }
        .container { background-color: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); width: 100%; max-width: 900px; margin: 0 auto; }
        h3, h5 { margin-top: 0; }
        .ticket-detail-grid { display: grid; grid-template-columns: 150px 1fr; gap: 8px 16px; align-items: center; }
        .ticket-detail-grid .label { color: #65676b; font-weight: 600; }
        .ticket-detail-grid .value { font-weight: 500; }
        hr { border: 0; border-top: 1px solid #ced0d4; margin: 1.5rem 0; }
        
        /* Gaya Komentar Mirip Facebook */
        .comments-section { margin-top: 1.5rem; }
        .comment { display: flex; gap: 10px; margin-bottom: 1rem; }
        .comment-avatar { width: 40px; height: 40px; border-radius: 50%; background-color: #e4e6eb; flex-shrink: 0; }
        .comment-body { background-color: #f0f2f5; border-radius: 18px; padding: 8px 12px; width: 100%; }
        .comment-author { font-weight: 600; font-size: 0.9rem; color: #050505; }
        .comment-text { font-size: 0.95rem; margin: 2px 0 0; white-space: pre-wrap; word-wrap: break-word; }
        .comment-time { font-size: 0.8rem; color: #65676b; margin-top: 4px; }
        
        .comment-form textarea { width: 100%; padding: 8px 12px; border: 1px solid #ced0d4; border-radius: 18px; font-size: 1rem; box-sizing: border-box; resize: vertical; min-height: 40px; }
        .button { padding: 10px 16px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; width: 100%; box-sizing: border-box; }
        .button-primary { background-color: #1877f2; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Detail Tiket</h3>
        <div class="ticket-detail-grid">
            <div class="label">Nomor Tiket:</div> <div class="value"><strong>{{ $tiket->no_tiket }}</strong></div>
            <div class="label">Layanan:</div> <div class="value">{{ $tiket->layanan->nama ?? '[Layanan Dihapus]' }}</div>
            <div class="label">Unit Terkait:</div> <div class="value">{{ $tiket->layanan->unit->nama_unit ?? '[Unit Dihapus]' }}</div>
            <div class="label">Status Saat Ini:</div> 
            <div class="value">
                @php $latestStatus = $tiket->riwayatStatus->sortByDesc('created_at')->first(); @endphp
                <strong>{{ $latestStatus->status ?? 'Draft' }}</strong>
            </div>
            <div class="label">Deskripsi Awal:</div> <div class="value">{{ $tiket->deskripsi }}</div>
        </div>

        <hr>
        
        <div class="comments-section">
            <h5>Diskusi Tiket</h5>
            @forelse($tiket->komentars->sortBy('created_at') as $komentar)
                <div class="comment">
                    <div class="comment-avatar"></div>
                    <div class="comment-body">
                        <div class="comment-author">{{ $komentar->user->name }}</div>
                        {{-- PERBAIKAN: Menggunakan kolom 'komentar' yang benar dari database --}}
                        <div class="comment-text">{{ $komentar->komentar }}</div>
                        <div class="comment-time">{{ $komentar->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p style="color: #65676b;">Belum ada komentar.</p>
            @endforelse

            <div class="comment">
                <div class="comment-avatar"></div>
                <div class="comment-body" style="background: none; padding: 0;">
                    <form action="{{ route('mahasiswa.tiket.komentar.store', $tiket->id) }}" method="POST" class="comment-form">
                        @csrf
                        <textarea id="komentar" name="komentar" placeholder="Tulis balasan..." required></textarea>
                        <button type="submit" class="button button-primary" style="width: auto; padding: 6px 12px; margin-top: 8px;">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
<<<<<<< HEAD
    </div>
</div>
@endsection
=======
        
        <a href="{{ route('mahasiswa.tiket.index') }}" style="margin-top: 20px; display:inline-block;">Kembali ke Daftar Tiket</a>
    </div>
</body>
</html>

>>>>>>> helpdesk-polindra/main
