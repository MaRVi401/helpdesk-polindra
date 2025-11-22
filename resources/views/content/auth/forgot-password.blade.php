@extends('layouts.blankLayout')

@section('content')
<div class="authentication-wrapper authentication-cover">
    <div class="authentication-inner row m-0">

        <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
                <h4 class="mb-2">Lupa Kata Sandi? 🔒</h4>
                <p class="mb-4">Masukkan email Anda, dan kami akan mengirimkan link untuk mereset kata sandi Anda.</p>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                
                <form id="formAuthentication" class="mb-3" action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Institusi</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" 
                               placeholder="Masukkan email institusi Anda" 
                               value="{{ old('email') }}" required autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button class="btn btn-primary d-grid w-100" type="submit">Kirim Link Reset</button>
                </form>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                        <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
                        Kembali ke halaman login
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
