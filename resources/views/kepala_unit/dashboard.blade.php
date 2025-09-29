<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Kepala Unit</title>
</head>

<body>
    <h1>Dashboard Kepala Unit</h1>
    <p>Selamat datang, {{ Auth::user()->name }}. Anda bisa memonitor unit Anda di sini.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>

</html>
