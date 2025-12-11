@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola PIC Layanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepala Unit /</span> Manajemen PIC Layanan</h4>

    {{-- CEK APAKAH ADA UNIT YANG DIKIRIM DARI CONTROLLER --}}
    @if(isset($units) && $units->count() > 0)
        
        {{-- LOOPING UNTUK SETIAP UNIT (MEMBUAT TABEL TERPISAH) --}}
        @foreach($units as $unit)
            <div class="card mb-4">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="bx bx-building-house me-2"></i>Unit: {{ $unit->nama_unit }}</h5>
                </div>
                
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">Nama Layanan</th>
                                <th width="15%">Prioritas</th> {{-- KOLOM BARU --}}
                                <th>PIC Bertugas</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // FILTER LAYANAN HANYA UNTUK UNIT YANG SEDANG DI-LOOP
                                $unitLayanans = $layanans->where('unit_id', $unit->id);
                            @endphp

                            @forelse($unitLayanans as $layanan)
                            <tr>
                                <td>
                                    <span class="fw-bold text-heading">{{ $layanan->nama }}</span>
                                    
                                    {{-- Status Arsip tetap di sini (jika ada) --}}
                                    @if($layanan->status_arsip)
                                        <br><span class="badge bg-label-secondary mt-1" style="font-size: 0.7rem;">Diarsipkan</span>
                                    @endif
                                </td>
                                
                                {{-- KOLOM BARU: PRIORITAS --}}
                                <td>
                                    @php
                                        $prio = $layanan->prioritas;
                                        $prioLabel = match($prio) { 3 => 'Tinggi', 2 => 'Sedang', 1 => 'Rendah', default => '-' };
                                        $prioColor = match($prio) { 3 => 'danger', 2 => 'success', 1 => 'secondary', default => 'secondary' };
                                    @endphp
                                    <span class="badge bg-label-{{ $prioColor }}">{{ $prioLabel }}</span>
                                </td>

                                <td>
                                    @if($layanan->penanggungJawab->isEmpty())
                                        <span class="text-danger fst-italic small"><i class="bx bx-error-circle me-1"></i>Belum ada PIC</span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($layanan->penanggungJawab as $pic)
                                                <div class="d-flex align-items-center badge bg-label-info p-2">
                                                    <i class="bx bx-user me-1"></i>
                                                    <span>{{ \Illuminate\Support\Str::limit($pic->user->name ?? '-', 15) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kepala-unit.pic.edit', $layanan->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-cog me-1"></i> Kelola
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Belum ada layanan terdaftar di unit ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

    @else
        {{-- PESAN JIKA TIDAK ADA UNIT --}}
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            <div>
                Kepala belum di set ke PIC Unit
            </div>
        </div>
    @endif
</div>
@endsection