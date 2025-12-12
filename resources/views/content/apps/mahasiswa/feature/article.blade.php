@extends('layouts/layoutMaster')
@php
  $configData = Helper::appClasses();
@endphp

@section('title', 'Artikel Service Desk')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/plyr/plyr.scss'])
@endsection

@section('page-style')
  @vite('resources/assets/vendor/scss/pages/app-academy.scss')
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/plyr/plyr.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/management/feature-article.js')
@endsection

@section('content')
  <div class="app-academy">
    <div class="card p-0 mb-6">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-6">
        <div class="app-academy-md-25 card-body py-0 pt-6 ps-12">
          <img src="{{ asset('assets/img/illustrations/student-article.png') }}" alt="Student Article" height="180"
            class="scaleX-n1-rtl" />
        </div>
        <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center mb-6 py-6">
          <span class="card-title mb-4 lh-lg px-md-12 h4 text-heading">
            Edukasi, berita, dan informasi seputar teknologi <br> <span class="text-primary text-nowrap fw-bold">Dalam
              satu
              platform</span>.
          </span>
          <div class="d-flex align-items-center justify-content-between app-academy-md-80">
            <input type="search" placeholder="Cari artikel..." class="form-control me-4" id="searchArticle" />
            <button type="submit" class="btn btn-primary btn-icon"><i
                class="icon-base ti tabler-search icon-22px"></i></button>
          </div>
        </div>
        <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
          <img src="{{ asset('assets/img/illustrations/student-article-2.png') }}" alt="Student Article-2" height="188"
            class="scaleX-n1-rtl" />
        </div>
      </div>
    </div>

    <div class="card mb-6">
      <div class="card-header d-flex flex-wrap justify-content-between gap-4">
        <div class="card-title mb-0 me-1">
          <h5 class="mb-0">Artikel</h5>
          <p class="mb-0">Tersedia {{ $data_artikel->count() }} artikel yang dapat kamu baca</p>
        </div>
        <div class="d-flex justify-content-md-end align-items-center column-gap-6 flex-sm-row flex-column row-gap-4">
          <select class="form-select" id="categoryFilter" style="width: 200px;">
            <option value="">Semua Kategori</option>
            @foreach ($data_kategori as $kategori)
              <option value="{{ $kategori->id }}">{{ $kategori->kategori }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="card-body">
        <div class="row gy-6 mb-6" id="articleContainer">
          @forelse($data_artikel as $artikel)
            <div class="col-sm-6 col-lg-4 article-item" data-category="{{ $artikel->kategori_id }}"
              data-status="{{ $artikel->status }}">
              <div class="card p-2 h-100 shadow-none border">
                <div class="rounded-2 text-center mb-4">
                  {{-- GAMBAR --}}
                  <a href="{{ url('app/academy/course-details') }}">
                    @if ($artikel->gambar)
                      <img class="img-fluid rounded" src="{{ asset('storage/' . $artikel->gambar) }}"
                        alt="{{ $artikel->judul }}" />
                    @else
                      <img class="img-fluid rounded" src="{{ asset('assets/img/pages/thumbnail-default.png') }}"
                        alt="Default image" />
                    @endif
                  </a>
                </div>
                <div class="card-body p-4 pt-2">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    {{-- KATEGORI ARTIKEL --}}
                    <span class="badge bg-label-primary">{{ $artikel->kategori->kategori ?? 'Tidak diketahui' }}</span>
                  </div>
                  {{-- JUDUL ARTIKEL --}}
                  <h5 class="mb-2 line-clamp-2" title="{{ $artikel->judul }}">{{ $artikel->judul }}</h5>
                  {{-- DESKRIPSI  --}}
                  <p class="mt-1 line-clamp-3 mb-3" title="{{ strip_tags($artikel->deskripsi) }}">
                    {{ strip_tags($artikel->deskripsi) }}
                  </p>
                  <a href="{{ route('servicedesk.article.detail', $artikel->slug) }}" class="w-100 btn btn-primary">
                    Selengkapnya
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="card">
                <div class="card-body text-center py-5">
                  <i class="icon-base ti tabler-article-off icon-30px text-danger mb-3"></i>
                  <p class="text-muted">Belum ada artikel yang tersedia saat ini.</p>
                </div>
              </div>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
