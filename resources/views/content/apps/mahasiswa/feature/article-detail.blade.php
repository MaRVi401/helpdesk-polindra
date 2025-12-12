@extends('layouts/layoutMaster')

@section('title', $artikel->judul)

@section('page-script')
  @vite('resources/assets/js/management/feature-article.js')
@endsection

@section('content')
  <section class="section-py first-section-pt">
    <div class="container">
      <div class="row g-6">
        <div class="col-lg-8">
          {{-- JUDUL ARTIKEL --}}
          <h4 class="mb-2">{{ $artikel->judul }}</h4>
          {{-- NAMA PENULIS --}}
          <h6 class="badge bg-label-primary">Penulis: {{ $artikel->user->name ?? 'Admin' }}</h6>
          <p>{{ $artikel->created_at->translatedFormat('d F Y H:i') }}</p>
          <hr class="mt-6 mb-0" />
        </div>
      </div>
      <div class="row g-6 mt-2">
        <div class="col-lg-8">
          {{-- GAMBAR ARTIKEL --}}
          @if ($artikel->gambar)
            <div class="mb-6">
              <img src="{{ asset('storage/' . $artikel->gambar) }}" alt="{{ $artikel->judul }}"
                class="img-fluid w-100 border rounded" />
            </div>
          @else
            <div class="mb-6">
              <img src="{{ asset('assets/img/pages/thumbnail-default.png') }}" alt="{{ $artikel->judul }}"
                class="img-fluid w-100 border rounded" />
            </div>
          @endif
          {{-- DESKRIPSI --}}
          <div class="max-w-none">
            {!! $artikel->deskripsi !!}
          </div>
        </div>
        {{-- KOLOM KANAN: SIDEBAR REFERENSI --}}
        <div class="col-lg-4">
          {{-- REFERENSI ARTIKEL LAIN --}}
          <div class="bg-primary text-center py-2 px-4 rounded mb-4">
            <h5 class="mb-0 text-white">
              <i class="icon-base ti tabler-book me-2 icon-28px"></i>
              Referensi artikel lain
            </h5>
          </div>
          <ul class="list-unstyled mt-4 mb-0">
            @forelse($artikel_lain as $artikel)
              <li class="mb-4">
                <a href="{{ route('servicedesk.article.detail', $artikel->slug) }}"
                  class="text-heading d-flex justify-content-between align-items-start">
                  <div class="me-2">
                    <span class="d-block text-truncate mb-1">{{ $artikel->judul }}</span>
                    <small class="text-muted">{{ $artikel->created_at->translatedFormat('d F Y H:i') }}</small>
                  </div>
                  <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl text-body-secondary flex-shrink-0"></i>
                </a>
                <hr class="my-4">
              </li>
            @empty
              <li class="mb-4">
                <p class="text-muted mb-0">Belum ada artikel lain yang tersedia.</p>
                <hr class="my-4">
              </li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </section>
@endsection
