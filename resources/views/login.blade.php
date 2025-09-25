<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h1>Login Aplikasi Akademik</h1>
    @if($errors->any())
        <p style="color: red;">{{ $errors->first('msg') }}</p>
    @endif
    <a href="{{ route('google.login') }}">Masuk dengan Google</a>
</body>
</html>