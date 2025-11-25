@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola PIC Layanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepala Unit /</span> Manajemen PIC Layanan</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Unit: {{ $unit->nama_unit }}</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="40%">Nama Layanan</th>
                        <th>PIC Bertugas</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($layanans as $layanan)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $layanan->nama }}</span><br>
                            @php
                                $prio = $layanan->prioritas;
                                $prioLabel = match($prio) { 3 => 'Tinggi', 2 => 'Sedang', 1 => 'Rendah', default => '-' };
                                $prioColor = match($prio) { 3 => 'danger', 2 => 'success', 1 => 'secondary', default => 'secondary' };
                            @endphp
                            <span class="badge bg-label-{{ $prioColor }} mt-1">{{ $prioLabel }}</span>
                            
                            @if($layanan->status_arsip)
                                <span class="badge bg-label-secondary mt-1 ms-1">Diarsipkan</span>
                            @endif
                        </td>
                        <td>
                            @if($layanan->penanggungJawab->isEmpty())
                                <span class="text-danger fst-italic">Belum ada PIC</span>
                            @else
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($layanan->penanggungJawab as $pic)
                                        <span class="badge bg-label-info" data-bs-toggle="tooltip" title="{{ $pic->user->name }}">
                                            {{-- FIX ERROR DISINI: Gunakan namespace lengkap --}}
                                            {{ \Illuminate\Support\Str::limit($pic->user->name, 15) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            <!-- Tombol "Atur" sekarang menjadi Link ke halaman Edit -->
                            <a href="{{ route('kepala-unit.pic.edit', $layanan->id) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-cog me-1"></i> Kelola
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection