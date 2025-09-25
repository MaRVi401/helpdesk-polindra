<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
</head>

<body>
    <h1>Selamat Datang, {{ Auth::user()->name }}</h1>
    <p>Role Anda: <strong>{{ strtoupper(Auth::user()->role) }}</strong></p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>

</html>
