@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Route;
  $configData = Helper::appClasses();
  $hasPassword = !is_null(Auth::user()->password);
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Kelola Keamanan')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/set-security.js'])
@endsection

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
          <li class="nav-item">
            <a class="nav-link"
              href="{{ Route::has('user-profile.set-profile') ? route('user-profile.set-profile') : 'javascript:void(0);' }}"><i
                class="icon-base ti tabler-user icon-sm me-1_5"></i> Profil Akun</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="javascript:void(0);"><i class="icon-base ti tabler-lock icon-sm me-1_5"></i>
              Keamanan</a>
          </li>
        </ul>
      </div>
      {{-- UBAH PASSWORD --}}
      <div class="card mb-6">
        <h5 class="card-header">{{ $hasPassword ? 'Ubah Password' : 'Atur Password' }}</h5>
        <div class="card-body pt-1">
          <form id="formAccountSettings" method="POST" action="{{ route('user-profile.update-password') }}">
            @csrf
            @if ($hasPassword)
              <div class="row mb-sm-6 mb-2">
                <div class="col-md-6 form-password-toggle form-control-validation">
                  <label class="form-label" for="currentPassword">Password Saat Ini</label>
                  <div class="input-group input-group-merge">
                    <input class="form-control @error('currentPassword') is-invalid @enderror" type="password"
                      name="currentPassword" id="currentPassword"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                    <span class="input-group-text cursor-pointer"><i
                        class="icon-base ti tabler-eye-off icon-xs"></i></span>
                  </div>
                  @error('currentPassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            @endif
            <div class="row gy-sm-6 gy-2 mb-sm-0 mb-2">
              <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
                <label class="form-label" for="newPassword">Password Baru</label>
                <div class="input-group input-group-merge">
                  <input class="form-control @error('newPassword') is-invalid @enderror" type="password" id="newPassword"
                    name="newPassword"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off icon-xs"></i></span>
                </div>
                @error('newPassword')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
                <label class="form-label" for="confirmPassword">Konfirmasi Password Baru</label>
                <div class="input-group input-group-merge">
                  <input class="form-control @error('confirmPassword') is-invalid @enderror" type="password"
                    name="confirmPassword" id="confirmPassword"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off icon-xs"></i></span>
                </div>
                @error('confirmPassword')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <ul class="ps-4 mb-0">
              <li class="mb-3 text-warning">Minimal 6 karakter - semakin banyak semakin baik</li>
            </ul>
            <div class="mt-6">
              <button type="submit"
                class="btn btn-primary me-3">{{ $hasPassword ? 'Simpan Perubahan' : 'Atur Password' }}
              </button>
              <button type="reset" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-reload me-1"></i>
                Ulang
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  {{-- Success message dari session --}}
  @if (session('success'))
    <script>
      window.passwordSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  {{-- Error message dari session --}}
  @if (session('error'))
    <script>
      window.passwordErrorMessage = "{{ session('error') }}";
    </script>
  @endif
@endsection
