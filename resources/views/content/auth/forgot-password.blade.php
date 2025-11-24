@extends('layouts.blankLayout')

@section('title', 'Lupa Password Service Desk')

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
  @vite(['resources/assets/js/management/auth.js'])
@endsection

@section('content')
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container">
      <div class="authentication-inner">
        {{-- LUPA PASSWORD --}}
        @if (session('success'))
          <div class="bs-toast toast fade show w-100 mb-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              <i class="icon-base ti tabler-square-check-filled icon-xs me-2 text-success"></i>
              <div class="me-auto fw-medium">Berhasil</div>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">{{ session('success') }}</div>
          </div>
        @endif
        @if ($errors->any())
          <div class="bs-toast toast fade show w-100 mb-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              <i class="icon-base ti tabler-bell icon-xs me-2 text-primary"></i>
              <div class="me-auto fw-medium">Gagal</div>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
              @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          </div>
        @endif
        <div class="card">
          <div class="card-body">
            {{-- LOGO --}}
            <div class="app-brand justify-content-center mb-6">
              <a href="{{ url('/') }}" class="app-brand-link">
                <img class="wpx-200" src="{{ asset('assets/img/logo/service_desk.svg') }}" alt="Service Desk">
              </a>
            </div>
            <p class="mb-6 fs-7">Silakan masukkan alamat email Kamu. Kami akan mengirimkan link reset password melalui
              email.</p>
            {{-- LUPA PASSWORD FORM --}}
            <form id="formAuthentication" class="mb-4" action="{{ route('password.email') }}" method="POST">
              @csrf
              <div class="mb-6 form-control-validation">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" required
                  placeholder="Masukkan alamat email" value="{{ old('email') }}" autofocus />
              </div>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="my-5">
                <div class="d-flex justify-content-between">
                  <div class="d-flex justify-content-between">
                    <a href="{{ route('login') }}">
                      <small>Kembali ke halaman Login</small>
                    </a>
                  </div>
                </div>
              </div>
              <div class="mb-5">
                <button class="btn btn-primary d-grid w-100" type="submit">Kirim Email</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
