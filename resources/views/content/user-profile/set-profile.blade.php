@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Route;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Kelola Profil')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/set-profile.js'])
@endsection

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
          <li class="nav-item">
            <a class="nav-link active" href="javascript:void(0);"><i
                class="icon-base ti tabler-user-search icon-sm me-1_5"></i>
              Profil Akun</a>
          </li>
          <li class="nav-item">
            <a class="nav-link"
              href="{{ Route::has('user-profile.set-security') ? route('user-profile.set-security') : 'javascript:void(0);' }}"><i
                class="icon-base ti tabler-lock icon-sm me-1_5"></i> Keamanan</a>
          </li>
        </ul>
      </div>
      <div class="card mb-6">
        {{-- KELOLA PROFIL --}}
        <div class="card-body">
          <div class="d-flex align-items-start align-items-sm-center gap-6">
            @php
              $avatarUrl = Auth::user()->avatar
                  ? asset('storage/avatar/' . Auth::user()->avatar)
                  : Auth::user()->profile_photo_url;
            @endphp
            <img src="{{ $avatarUrl }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded"
              id="uploadedAvatar" />
            <div class="button-wrapper">
              <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                <i class="icon-base ti tabler-upload me-1_5"></i> Unggah Avatar
                <input type="file" id="upload" class="account-file-input" hidden
                  accept="image/png, image/jpeg, image/jpg" />
              </label>
              <button type="button" class="btn btn-label-secondary account-image-reset mb-4">
                <i class="icon-base ti tabler-refresh"></i>
              </button>
              <div>Format JPEG, PNG dan JPG. Ukuran maksimal 800kb.</div>
            </div>
          </div>
        </div>
        <div class="card-body pt-4">
          <form id="formAccountSettings" method="POST" action="{{ route('user-profile.update') }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- Hidden input untuk avatar --}}
            <input type="file" id="avatarInput" name="avatar" hidden />
            <div class="row gy-4 gx-6 mb-6">
              <div class="col-md-6 form-control-validation">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name"
                  name="name" placeholder="Masukkan Nama Lengkap" value="{{ old('name', Auth::user()->name) }}" />
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email Institusi</label>
                <input class="form-control" type="email" id="email" value="{{ Auth::user()->email }}" disabled
                  readonly />
                <small class="text-warning">Email institusi tidak dapat diubah</small>
              </div>
              <div class="col-md-6">
                <label for="no_wa" class="form-label">WhatsApp</label>
                <input class="form-control @error('no_wa') is-invalid @enderror" type="text" id="no_wa"
                  name="no_wa" placeholder="Masukkan No. WhatsApp (08xxxxxxxxxx)" maxlength="13"
                  value="{{ old('no_wa', Auth::user()->no_wa) }}" />
                @error('no_wa')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label for="email_personal" class="form-label">Email Personal</label>
                <input class="form-control @error('email_personal') is-invalid @enderror" type="email"
                  id="email_personal" name="email_personal" placeholder="Masukkan Email Personal"
                  value="{{ old('email_personal', Auth::user()->email_personal) }}" />
                @error('email_personal')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary me-3">
                Simpan Perubahan
              </button>
              <a href="{{ route('user-profile.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i>
                Kembali
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Success message dari session --}}
  @if (session('success'))
    <script>
      window.profileSuccessMessage = "{{ session('success') }}";
    </script>
  @endif

  {{-- Error message dari session --}}
  @if (session('error'))
    <script>
      window.profileErrorMessage = "{{ session('error') }}";
    </script>
  @endif
@endsection
