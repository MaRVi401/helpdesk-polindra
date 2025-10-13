<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Mahasiswa</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        nav ul { list-style: none; padding: 0; }
        nav li { margin-bottom: 10px; }
        nav a { text-decoration: none; color: #007bff; font-size: 1.2rem; }
        nav a:hover { text-decoration: underline; }
        hr { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Selamat Datang di Dashboard Mahasiswa, {{ Auth::user()->name }}!</h1>
    <p>Ini adalah halaman khusus untuk Anda. Silakan pilih menu di bawah.</p>

    <hr>

    <h3>Menu Layanan</h3>
    <nav>
        <ul>
            <li><a href="{{ route('mahasiswa.tiket.create') }}">Buat Tiket Layanan Baru</a></li>
            <li><a href="{{ route('mahasiswa.tiket.index') }}">Lihat Daftar Tiket Saya</a></li>
        </ul>
    </nav>

    <hr>
    
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
