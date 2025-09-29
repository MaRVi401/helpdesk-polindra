<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Mahasiswa</title>
</head>

<body>
    <h1>Selamat Datang di Dashboard Mahasiswa, {{ Auth::user()->name }}!</h1>
    <p>Ini adalah halaman khusus untuk Anda.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>

</html>
