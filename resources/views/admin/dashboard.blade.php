<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Admin</title>
</head>

<body>
    <h1>Selamat Datang di Dashboard Admin, {{ Auth::user()->name }}!</h1>
    <p>Anda bisa mengelola seluruh sistem dari sini.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>

</html>
