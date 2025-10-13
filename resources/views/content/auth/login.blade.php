@php
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login Servicedesk Polindra')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-6">
        {{-- LOGIN --}}
        <div class="card">
          <div class="card-body">
            {{-- LOGO --}}
            <div class="app-brand justify-content-center mb-6">
              <a href="{{ url('/') }}" class="app-brand-link">
                <img class="wpx-200" src="{{ asset('assets/img/logo/service_desk.svg') }}" alt="Google">
              </a>
            </div>
            <p class="mb-6 fs-7">Silakan login ke akun kamu untuk mengajukan permintaan layanan atau melaporkan kendala
              kampus di Service Desk.</p>
            <div class="mb-6">
              <a class="btn bg-white border d-flex align-items-center justify-content-center gap-2 w-100"
                href="{{ route('google.login') }}">

                <img class="wpx-25" src="{{ asset('assets/img/icons/brands/google.png') }}" alt="Google">
                <span>Login dengan Google</span>
                </button>
              </a>
            </div>
            <div class="divider my-6">
              <div class="divider-text">atau</div>
            </div>
            {{-- LOGIN FORM --}}
            <form id="formAuthentication" class="mb-4" action="{{ route('login') }}" method="POST">
              @csrf
              <div class="mb-6 form-control-validation">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" required
                  placeholder="Masukkan alamat email" autofocus />
              </div>
              <div class="mb-6 form-password-toggle form-control-validation">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" required
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
              </div>
              <div class="my-8">
                <div class="d-flex justify-content-between">
                  <div class="form-check mb-0 ms-2">
                    <input class="form-check-input" type="checkbox" id="remember-me" />
                    <label class="form-check-label" for="remember-me"> Ingat saya </label>
                  </div>
                  <a href="javascript:void(0);">
                    <p class="mb-0">Lupa Password?</p>
                  </a>
                </div>
              </div>
              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
