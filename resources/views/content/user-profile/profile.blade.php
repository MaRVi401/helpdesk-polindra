@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Route;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Profil Saya')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss'])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app-user-view-account.js'])
@endsection

@section('content')
  {{-- HEADER --}}
  <div class="row">
    <div class="col-12">
      <div class="card mb-6">
        <div class="user-profile-header-banner">
          <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top" />
        </div>
        <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
          <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
            @php
              $avatarUrl = Auth::user()->avatar
                  ? asset('storage/avatar/' . Auth::user()->avatar)
                  : Auth::user()->profile_photo_url;
            @endphp
            <img src="{{ $avatarUrl }}" alt="user image" class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img" />
          </div>
          <div class="flex-grow-1 mt-3 mt-lg-5">
            <div
              class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
              <div class="user-profile-info">
                <h4 class="mb-2 mt-lg-6">{{ Auth::user()->name }}</h4>
                <ul
                  class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                  <li class="list-inline-item d-flex gap-2 align-items-center"><i
                      class="icon-base ti tabler-user-star icon-lg text-dark"></i><span
                      class="fw-medium">{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</span></li>
                </ul>
              </div>
              <a href="{{ Route::has('user-profile.set-profile') ? route('user-profile.set-profile') : 'javascript:void(0);' }}"
                class="btn btn-primary mb-1"> <i class="icon-base ti tabler-settings-share icon-xs me-2"></i>Kelola
                Profil</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- PROFIL --}}
  <div class="row">
    <div class="col-12">
      <div class="card mb-6">
        <div class="card-body">
          <p class="card-text text-uppercase text-body-secondary small mb-0">Profil saya</p>
          <ul class="list-unstyled my-3 py-1">
            {{-- NIM atau NIK --}}
            <li class="mb-4">
              <div class="d-flex align-items-center mb-1 text-dark">
                <i class="icon-base ti tabler-id icon-lg"></i>
                <span class="fw-medium ms-2">
                  {{ Auth::user()->role === 'mahasiswa' ? 'NIM' : 'NIK' }}
                </span>
              </div>
              <span>
                @if (Auth::user()->role === 'mahasiswa')
                  {{ Auth::user()->mahasiswa->nim ?? '-' }}
                @else
                  {{ Auth::user()->staff->nik ?? '-' }}
                @endif
              </span>
              <hr>
            </li>
            <li class="mb-4">
              <div class="d-flex align-items-center mb-1 text-dark">
                <i class="icon-base ti tabler-mail icon-lg"></i>
                <span class="fw-medium ms-2">Email</span>
              </div>
              <span>{{ Auth::user()->email }}</span>
              <hr>
            </li>
            {{-- Program Studi & Jurusan khusus mahasiswa --}}
            @if (Auth::user()->role === 'mahasiswa')
              <li class="mb-4">
                <div class="d-flex align-items-center mb-1 text-dark">
                  <i class="icon-base ti tabler-school icon-lg"></i>
                  <span class="fw-medium ms-2">Program Studi</span>
                </div>
                <span
                  class="badge bg-label-primary">{{ Auth::user()->mahasiswa->programStudi->program_studi ?? '-' }}</span>
                <hr>
              </li>
              <li class="mb-4">
                <div class="d-flex align-items-center mb-1 text-dark">
                  <i class="icon-base ti tabler-building icon-lg"></i>
                  <span class="fw-medium ms-2">Jurusan</span>
                </div>
                <span
                  class="badge bg-label-primary">{{ Auth::user()->mahasiswa->programStudi->jurusan->nama_jurusan ?? '-' }}</span>
                <hr>
              </li>
              <li class="mb-4">
                <div class="d-flex align-items-center mb-1 text-dark">
                  <i class="icon-base ti tabler-calendar-time icon-lg"></i>
                  <span class="fw-medium ms-2">Tahun Masuk</span>
                </div>
                <span>{{ Auth::user()->mahasiswa->tahun_masuk ?? '-' }}</span>
                <hr>
              </li>
            @endif
            <li class="mb-4">
              <div class="d-flex align-items-center mb-1 text-dark">
                <i class="icon-base ti tabler-calendar-check icon-lg"></i>
                <span class="fw-medium ms-2">Bergabung</span>
              </div>
              <span>{{ Auth::user()->created_at->format('d M Y H:i') }}</span>
              <hr>
            </li>
          </ul>
          <p class="card-text text-uppercase text-body-secondary small mb-0">Kontak saya</p>
          <ul class="list-unstyled my-3 py-1">
            <li class="mb-4">
              <div class="d-flex align-items-center mb-1 text-dark">
                <i class="text-danger icon-base ti tabler-mail-forward icon-lg"></i>
                <span class="fw-medium ms-2">Email Personal</span>
              </div>
              <span>{{ Auth::user()->email_personal ?? 'Belum ditambahkan' }}</span>
              <hr>
            </li>
            <li class="mb-4">
              <div class="d-flex align-items-center mb-1 text-dark">
                <i class="text-success icon-base ti tabler-brand-whatsapp icon-lg"></i>
                <span class="fw-medium ms-2">No WhatsApp</span>
              </div>
              <span>{{ Auth::user()->no_wa ?? 'Belum ditambahkan' }}</span>
              <hr>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection
