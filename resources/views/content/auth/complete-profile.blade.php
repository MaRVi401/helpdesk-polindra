@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Lengkapi Profil')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/management/complete-profile.js'])
@endsection

@section('content')
  <div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner border row">
      <div class="d-flex col-12 align-items-center justify-content-center authentication-bg p-5">
        <div class="w-px-700">
          {{-- LOGO - Dipindahkan ke dalam container form --}}
          <div class="text-center mt-4 mb-4">
            <img class="img-fluid w-px-200" src="{{ asset('assets/img/logo/service_desk.svg') }}" alt="Logo Service Desk">
          </div>

          <div id="StepsValidation" class="bs-stepper border-none shadow-none mt-5 mb-5">
            <div class="bs-stepper-header border-none pt-12 px-0">
              {{-- STEP OTENTIKASI (penanda otentikasi pengguna) --}}
              <div class="step active" data-target="#authenticationSuccessful">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle"><i class="icon-base ti tabler-lock-check icon-md"></i></span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Otentikasi</span>
                    <span class="bs-stepper-subtitle">Berhasil Terhubung</span>
                  </span>
                </button>
              </div>
              <div class="line">
                <i class="icon-base ti tabler-chevron-right"></i>
              </div>
              {{-- STEP VALIDASI --}}
              <div class="step" data-target="#InfoValidation">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle"><i class="icon-base ti tabler-user icon-md"></i></span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Validasi</span>
                    <span class="bs-stepper-subtitle">Lengkapi Profil</span>
                  </span>
                </button>
              </div>
            </div>
            {{-- FORM LENGKAPI PROFIL --}}
            <div class="bs-stepper-content px-0">
              <form id="StepsForm" action="{{ route('save.complete.profile') }}" method="POST">
                @csrf
                {{-- STEP OTENTIKASI (kosong, hanya penanda) --}}
                <div id="authenticationSuccessful" class="content">
                  {{-- Konten kosong karena step ini hanya penanda --}}
                </div>
                {{-- STEP INFORMASI PROFIL --}}
                <div id="InfoValidation" class="content">
                  <div class="content-header mb-6">
                    <h4 class="mb-0">Lengkapi Profil</h4>
                    <p class="mb-0">Silakan lengkapi data profil Kamu</p>
                  </div>
                  {{-- ERROR MESSAGES --}}
                  @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                      <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
                  <div class="row g-6">
                    {{-- NIM  --}}
                    <div class="col-sm-6 form-control-validation">
                      <label class="form-label" for="nim">Nomor Induk Mahasiswa</label>
                      <div class="input-group">
                        <span class="input-group-text">NIM</span>
                        @php
                          $isStudentEmail = str_contains(auth()->user()->email, '@student.polindra.ac.id');
                          $nimValue = !empty($autoNIM) ? $autoNIM : old('nim');
                          $nimReadonly = !empty($autoNIM) ? 'readonly' : '';
                        @endphp
                      <input type="text" name="nim" id="nim" class="form-control"
                          value="{{ $nimValue }}" placeholder="Masukkan NIM" {{ $nimReadonly }} required />
                        @if (!empty($autoNIM))
                          <span class="input-group-text text-success">
                            <i class="icon-base ti tabler-shield-check-filled"></i>
                          </span>
                        @elseif($isStudentEmail && empty($autoNIM))
                          <span class="input-group-text">
                            <i class="icon-base ti tabler-pencil-search"></i>
                          </span>
                        @endif
                      </div>
                      {{-- INFO TEXT --}}
                      @if (!empty($autoNIM))
                        <small class="text-success mt-1 d-block">
                          <i class="icon-base ti tabler-check"></i>
                          NIM Kamu telah terdeteksi.
                        </small>
                      @elseif($isStudentEmail && empty($autoNIM))
                        <small class="text-warning mt-1 d-block">
                          <i class="icon-base ti tabler-info-circle"></i>
                          Email student terdeteksi, namun NIM tidak terdeteksi. Harap masukkan NIM manual.
                        </small>
                      @else
                        <small class="text-muted mt-1 d-block">
                          <i class="icon-base ti tabler-info-circle"></i>
                          Masukkan NIM Kamu
                        </small>
                      @endif
                    </div>
                    {{-- PROGRAM STUDI --}}
                    <div class="col-sm-6 form-control-validation">
                      <label class="form-label" for="program_studi_id">Program Studi</label>
                      <select name="program_studi_id" id="program_studi_id" class="select2 form-select"
                        data-allow-clear="true" required>
                        <option value="" disabled selected>Pilih Program Studi</option>
                        @foreach ($programStudi as $prodi)
                          <option value="{{ $prodi->id }}"
                            {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->program_studi }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    {{-- TAHUN MASUK --}}
                    <div class="col-sm-6 form-control-validation">
                      <label class="form-label" for="tahun_masuk">Tahun Masuk</label>
                      <select name="tahun_masuk" id="tahun_masuk" class="select2 form-select" data-allow-clear="true"
                        required>
                        <option value="" disabled selected>Pilih Tahun</option>
                        @foreach ($years as $year)
                          <option value="{{ $year }}" {{ old('tahun_masuk') == $year ? 'selected' : '' }}>
                            {{ $year }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    {{-- BUTTON --}}
                    <div class="col-12 d-flex justify-content-between mt-4">
                      <button type="submit" class="btn btn-primary btn-submit">Simpan Profil</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="module">
    // Check selected custom option
    window.Helpers.initCustomOptionCheck();
  </script>
@endsection
