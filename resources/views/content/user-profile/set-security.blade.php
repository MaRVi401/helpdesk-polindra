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
            <h6 class="text-body">Saran Password:</h6>
            <ul class="ps-4 mb-0">
              <li class="mb-4">Minimal 8 karakter - semakin banyak semakin baik</li>
              <li class="mb-4">Setidaknya satu karakter huruf kecil</li>
              <li>Setidaknya satu angka, simbol, atau karakter spasi</li>
            </ul>
            <div class="mt-6">
              <button type="submit"
                class="btn btn-primary me-3">{{ $hasPassword ? 'Simpan Perubahan' : 'Atur Password' }}</button>
              <button type="reset" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-reload me-1"></i>
                Ulang</button>
            </div>
          </form>
        </div>
      </div>
      <!-- Recent Devices -->
      {{-- <div class="card mb-6">
        <h5 class="card-header">Recent Devices</h5>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th class="text-truncate">Browser</th>
                <th class="text-truncate">Device</th>
                <th class="text-truncate">Location</th>
                <th class="text-truncate">Recent Activities</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-truncate text-heading fw-medium"><i
                    class="icon-base ti tabler-brand-windows icon-md align-top text-info me-2"></i>Chrome on Windows</td>
                <td class="text-truncate">HP Spectre 360</td>
                <td class="text-truncate">Switzerland</td>
                <td class="text-truncate">10, July 2021 20:07</td>
              </tr>
              <tr>
                <td class="text-truncate text-heading fw-medium"><i
                    class="icon-base ti tabler-device-mobile icon-md  align-top text-danger me-2"></i>Chrome on iPhone
                </td>
                <td class="text-truncate">iPhone 12x</td>
                <td class="text-truncate">Australia</td>
                <td class="text-truncate">13, July 2021 10:10</td>
              </tr>
              <tr>
                <td class="text-truncate text-heading fw-medium"><i
                    class="icon-base ti tabler-brand-android icon-md align-top text-success me-2"></i>Chrome on Android
                </td>
                <td class="text-truncate">Oneplus 9 Pro</td>
                <td class="text-truncate">Dubai</td>
                <td class="text-truncate">14, July 2021 15:15</td>
              </tr>
              <tr>
                <td class="text-truncate text-heading fw-medium"><i
                    class="icon-base ti tabler-brand-apple icon-md align-top me-2"></i>Chrome on MacOS</td>
                <td class="text-truncate">Apple iMac</td>
                <td class="text-truncate">India</td>
                <td class="text-truncate">16, July 2021 16:17</td>
              </tr>
              <tr>
                <td class="text-truncate text-heading fw-medium"><i
                    class="icon-base ti tabler-brand-windows icon-md align-top text-warning me-2"></i>Chrome on Windows
                </td>
                <td class="text-truncate">HP Spectre 360</td>
                <td class="text-truncate">Switzerland</td>
                <td class="text-truncate">20, July 2021 21:01</td>
              </tr>
              <tr class="border-transparent">
                <td class="text-truncate text-heading fw-medium"><i
                    class="icon-base ti tabler-brand-android icon-md align-top text-success me-2"></i>Chrome on Android
                </td>
                <td class="text-truncate">Oneplus 9 Pro</td>
                <td class="text-truncate">Dubai</td>
                <td class="text-truncate">21, July 2021 12:22</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div> --}}
      <!--/ Recent Devices -->
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
