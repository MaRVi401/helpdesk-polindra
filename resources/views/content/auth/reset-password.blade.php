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
            <p class="mb-6 fs-7">Silakan masukkan password baru Kamu. Pastikan kuat dan mudah diingat</p>
            {{-- LUPA PASSWORD FORM --}}
            <form id="formAuthentication" class="mb-4" action="{{ route('password.update') }}" method="POST">
              @csrf
              <input type="hidden" name="token" value="{{ $token }}">
              <div class="mb-6 form-control-validation">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" required
                  placeholder="Masukkan alamat email" value="{{ old('email', request('email')) }}" readonly />
              </div>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="mb-6 form-password-toggle form-control-validation">
                <label class="form-label" for="password">Password Baru</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" required
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
              </div>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="mb-6 form-password-toggle form-control-validation">
                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password_confirmation" class="form-control" name="password_confirmation"
                    required placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
              </div>
              @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="mb-5">
                <button class="btn btn-primary d-grid w-100" type="submit">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
