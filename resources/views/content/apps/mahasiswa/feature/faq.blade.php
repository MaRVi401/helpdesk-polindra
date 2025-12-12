@php
  $configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')

@section('title', 'Service Desk FAQ')

@section('page-style')
  @vite('resources/assets/vendor/scss/pages/page-faq.scss')
@endsection
@section('content')

  <div class="faq-header d-flex flex-column justify-content-center align-items-center h-px-300 position-relative">
    <img src="{{ asset('assets/img/pages/faq-banner.png') }}" class="scaleX-n1-rtl faq-banner-img z-n1 rounded"
      alt="background image" />
    <h4 class="text-center text-white mb-2">Hai, ada yang bisa kami bantu?</h4>
    <p class="text-center text-white mb-0 px-4">Pilih kategori untuk menemukan bantuan yang Kamu butuhkan dengan cepat.</p>
  </div>
  <div class="row mt-6">
    {{-- NAVIGATION --}}
    <div class="col-lg-3 col-md-4 col-12 mb-md-0 mb-4">
      <div class="d-flex justify-content-between flex-column nav-align-left mb-2 mb-md-0">
        <ul class="nav nav-pills flex-column">
          @php
            $layanan_list = $data_faq->groupBy('layanan_id')->map(function ($items) {
                return $items->first()->layanan;
            });
          @endphp
          @foreach ($layanan_list as $index => $layanan)
            <li class="nav-item">
              <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                data-bs-target="#layanan-{{ $layanan->id }}">
                <i class="icon-base ti tabler-help icon-sm faq-nav-icon me-1_5"></i>
                <span class="align-middle">{{ $layanan->nama }}</span>
              </button>
            </li>
          @endforeach
        </ul>
        <div class="d-none d-md-block">
          <div class="mt-4">
            <img src="{{ asset('assets/img/illustrations/student-faq.png') }}" class="img-fluid" width="270"
              alt="Student FAQ" />
          </div>
        </div>
      </div>
    </div>
    {{-- FAQ --}}
    <div class="col-lg-9 col-md-8 col-12">
      <div class="tab-content p-0">
        @foreach ($layanan_list as $index => $layanan)
          <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="layanan-{{ $layanan->id }}"
            role="tabpanel">
            <div class="d-flex mb-4 gap-4 align-items-center">
              <div>
                <span class="badge bg-label-primary rounded h-px-50 py-2">
                  <i class="icon-base ti tabler-help icon-30px"></i>
                </span>
              </div>
              <div>
                <h5 class="mb-0">
                  <span class="align-middle">{{ $layanan->nama }}</span>
                </h5>
                <span>Dapatkan bantuan dari {{ strtolower($layanan->nama) }}</span>
              </div>
            </div>
            <div id="accordion-{{ $layanan->id }}" class="accordion">
              @php
                $faqs = $data_faq->where('layanan_id', $layanan->id);
              @endphp
              @foreach ($faqs as $faq_index => $faq)
                <div class="card accordion-item {{ $faq_index == 0 ? 'active' : '' }}">
                  <h2 class="accordion-header">
                    <button class="accordion-button {{ $faq_index == 0 ? '' : 'collapsed' }}" type="button"
                      data-bs-toggle="collapse" aria-expanded="{{ $faq_index == 0 ? 'true' : 'false' }}"
                      data-bs-target="#accordion-{{ $layanan->id }}-{{ $faq->id }}"
                      aria-controls="accordion-{{ $layanan->id }}-{{ $faq->id }}">
                      {{ $faq->judul }}
                    </button>
                  </h2>
                  <div id="accordion-{{ $layanan->id }}-{{ $faq->id }}"
                    class="accordion-collapse collapse {{ $faq_index == 0 ? 'show' : '' }}">
                    <div class="accordion-body">
                      {!! nl2br(e($faq->deskripsi)) !!}
                    </div>
                  </div>
                </div>
              @endforeach
              @if ($faqs->isEmpty())
                <div class="card">
                  <div class="card-body text-center py-5">
                    <p class="mb-0 text-muted">Belum ada FAQ untuk kategori ini</p>
                  </div>
                </div>
              @endif
            </div>
          </div>
        @endforeach
        @if ($layanan_list->isEmpty())
          <div class="alert alert-info">
            <p class="mb-0">Belum ada data FAQ yang tersedia.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
  {{-- KONTAK --}}
  <div class="row my-6">
    <div class="col-12 text-center my-6">
      <h4 class="my-2">Masih ada pertanyaan?</h4>
      <p class="mb-0">Jika Kamu tidak menemukan pertanyaan di FAQ kami, Kamu dapat menghubungi kami. Kami akan segera
        menjawab Kamu.</p>
    </div>
  </div>
  <div class="row justify-content-center gap-md-0 gap-6">
    <div class="col-md-6">
      <div class="py-6 rounded bg-faq-section text-center">
        <span class="badge bg-label-primary p-2">
          <i class="icon-base ti tabler-phone icon-26px mx-50 mt-50"></i>
        </span>
        <h5 class="mt-4 mb-1"><a class="text-heading" href="tel:+6285171215103">+62 851 712 15103</a></h5>
        <p class="mb-0">Kami selalu senang membantu.</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="py-6 rounded bg-faq-section text-center">
        <span class="badge bg-label-primary p-2">
          <i class="icon-base ti tabler-mail icon-26px mx-50 mt-50"></i>
        </span>
        <h5 class="mt-4 mb-1"><a class="text-heading"
            href="mailto:servicedesk@polindra.ac.id">servicedesk@polindra.ac.id</a></h5>
        <p class="mb-0">Cara terbaik untuk mendapatkan jawaban cepat</p>
      </div>
    </div>
  </div>
@endsection
