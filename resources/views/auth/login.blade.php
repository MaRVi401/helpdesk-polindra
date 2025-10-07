@extends('layouts.app')
@section('title', 'Login Helpdesk')
@push('page-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
@endpush

@section('content')
    <div class="authentication-wrapper authentication-cover">
        {{-- LOGO --}}
        <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
            <span class="app-brand-logo demo">
            </span>
            <span class="app-brand-text demo text-heading fw-bold">Vuexy</span>
        </a>
        <div class="authentication-inner row m-0">
            {{-- LEFT ILLUSTRATIONS --}}
            <div class="d-none d-lg-flex col-lg-8 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/auth-login-illustration-light.png') }}"
                        alt="auth-login-cover" class="my-5 auth-illustration" />
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-login-cover"
                        class="platform-bg" />
                </div>
            </div>
            {{-- LOGIN FORM --}}
            <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    {{-- ALERT --}}
                    @if (session('error'))
                        <x-alert>{{ session('error') }}</x-alert>
                    @endif
                    @error('email')
                        <x-alert>{{ $message }}</x-alert>
                    @enderror
                    <h4 class="mb-1">Selamat Datang di-Helpdesk POLINDRA 👋</h4>
                    <p class="mb-6">Silakan masuk ke akun Anda dan coba Layanan</p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-6">
                            <label for="email" class="form-label">
                                <i class="ti ti-mail me-1"></i>Email</label>
                            <input type="text" id="email" name="email" required class="form-control"
                                placeholder="Contoh: helpdesk@email.com" autofocus>
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="password"><i class="ti ti-password-user me-1"></i>Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" name="password" required class="form-control"
                                    placeholder="Masukkan password">
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="my-8 d-flex justify-content-between">
                            <div class="form-check mb-0 ms-2">
                                <input class="form-check-input" type="checkbox" id="remember-me">
                                <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                            </div>
                            <a>
                                <p class="mb-0">Lupa Password?</p>
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100">Masuk</button>
                    </form>
                    <p class="text-center mt-4">
                        <span>atau</span>
                    </p>
                    <div class="divider my-4">
                        <div class="divider-text text-muted fst-normal">Masuk dengan akun Google Anda</div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('google.login') }}">
                            <img src="{{ asset('assets/img/icons/brands/google.png') }}" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('page-js')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
@endpush
