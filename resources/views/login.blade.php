<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f4f4f4; }
        .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        input { width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.7rem; border: none; border-radius: 4px; background-color: #007bff; color: white; cursor: pointer; }
        .google-btn { background-color: #DB4437; margin-top: 1rem; display: block; text-decoration: none; padding: 0.7rem; color: white; border-radius: 4px;}
        .error { color: red; text-align: left; font-size: 0.9em; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login Aplikasi</h1>

        {{-- Menampilkan error validasi --}}
        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Form Login Manual --}}
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            
            <div>
                <input type="email" name="email" placeholder="Email" required autofocus>
            </div>

            <div>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div>
                <button type="submit">Login</button>
            </div>
        </form>

        <hr style="margin: 1.5rem 0;">

        {{-- Tombol Login Google --}}
        <a href="{{ route('google.login') }}" class="google-btn">Masuk dengan Google</a>
    </div>
</body>
</html>