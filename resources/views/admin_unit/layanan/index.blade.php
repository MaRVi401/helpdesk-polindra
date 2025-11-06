@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola Layanan Unit')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Admin Unit /</span> Kelola Layanan
</h4>

<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title">Filter Layanan</h5>
    <form action="{{ route('admin_unit.layanan.index') }}" method="GET">
      <div class="row">
        <div class="col-md-4">
          <label for="id_unit" class="form-label">Unit</label>
          <select class="form-select" id="id_unit" name="id_unit">
            <option value="">Semua Unit</option>
            @foreach($units as $unit)
              <option value="{{ $unit->id }}" {{ request('id_unit') == $unit->id ? 'selected' : '' }}>
                {{ $unit->nama_unit }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button type="submit" class="btn btn-primary me-2">Cari</button>
          <a href="{{ route('admin_unit.layanan.index') }}" class="btn btn-secondary">Reset</a>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daftar Layanan</h5>
    <a href="{{ route('admin_unit.layanan.create') }}" class="btn btn-primary">Tambah Layanan</a>
  </div>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Nama Layanan</th>
          <th>Unit</th> <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @forelse ($layanan as $item)
        <tr>
          <td>
            <span class="fw-medium">{{ $item->nama_layanan }}</span>
            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($item->deskripsi, 100) }}</div>
          </td>
          
          <td>{{ $item->unit?->nama_unit ?? 'N/A' }}</td> <td>
            @if ($item->status == 'Aktif')
              <span class="badge bg-label-success">{{ $item->status }}</span>
            @else
              <span class="badge bg-label-secondary">{{ $item->status }}</span>
            @endif
          </td>
          <td>
            <div class="d-flex">
              <a class="btn btn-sm btn-info me-2" href="{{ route('admin_unit.layanan.edit', $item->id) }}">Edit</a>
              <form action="{{ route('admin_unit.layanan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center">Belum ada layanan yang ditambahkan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-center mt-4">
    {{ $layanan->links() }}
  </div>
</div>
@endsection