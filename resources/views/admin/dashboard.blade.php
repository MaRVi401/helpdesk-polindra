<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Admin</title>
    <style>
        body {
            font-family: sans-serif;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav li {
            margin-bottom: 10px;
        }

        nav a {
            text-decoration: none;
            color: #007bff;
        }

        nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h1>Selamat Datang di Dashboard Admin, {{ Auth::user()->name }}!</h1>
    <p>Anda bisa mengelola seluruh sistem dari sini.</p>

    <hr>

    <h3>Manajemen Sistem</h3>
    <nav>
        <ul>
            {{-- masuk grup kelola pengguna --}}
            <li><a href="{{ route('admin.mahasiswa.index') }}">Kelola Data Mahasiswa</a></li>
            <li><a href="{{ route('admin.staff.index') }}">Kelola Data Staff</a></li>
            {{-- kelola faq --}}
            <li><a href="{{ route('admin.kelolafaq.index') }}">Kelola FAQ</a></li>
            {{-- kelola jurusan --}}
            <li><a href="{{ route('admin.jurusan.index') }}">Kelola Jurusan</a></li>
            {{-- kelola unit --}}
            <li><a href="{{ route('admin.unit.index') }}">Kelola Unit</a></li>
            {{-- masuk grup kelola artikel --}}
            <li><a href="{{ route('admin.artikel.index') }}">Kelola Artikel</a></li>
            <li><a href="{{ route('admin.kategori-artikel.index') }}">Kelola Kategori Artikel</a></li>

            {{-- Tambahkan link ke fitur admin lainnya --}}
        </ul>
    </nav>

    <hr>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>

</html>
