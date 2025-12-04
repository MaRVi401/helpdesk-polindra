@extends('layouts.layoutMaster')

@section('title', 'Detail Jabatan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Master Data / Jabatan /</span> Detail
    </h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Detail Jabatan: **{{ $data_position->nama_jabatan }}**</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $data_position->id }}</dd>

                <dt class="col-sm-3">Nama Jabatan</dt>
                <dd class="col-sm-9"><strong>{{ $data_position->nama_jabatan }}</strong></dd>

                <dt class="col-sm-3">Tanggal Dibuat</dt>
                <dd class="col-sm-9">{{ $data_position->created_at->format('d M Y H:i:s') }}</dd>

                <dt class="col-sm-3">Terakhir Diperbarui</dt>
                <dd class="col-sm-9">{{ $data_position->updated_at->format('d M Y H:i:s') }}</dd>
            </dl>

            <a href="{{ route('position.index') }}" class="btn btn-secondary mt-3">
                <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('position.edit', $data_position->id) }}" class="btn btn-primary mt-3">
                <i class="ti ti-pencil me-1"></i> Edit Jabatan
            </a>
        </div>
    </div>
</div>
@endsection