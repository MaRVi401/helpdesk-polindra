<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Admin Unit</title>
</head>

<body>
    <h1>Dashboard Admin Unit</h1>
    <p>Selamat datang, {{ Auth::user()->name }}. Halaman ini untuk mengelola tiket di unit Anda.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>

</html>
