@extends('layouts.contentNavbarLayout')

@section('title', 'Daftar Tiket Layanan (Admin)')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title">Daftar Tiket Layanan</h5>
            <a href="{{ route('ticket.create') }}" class="btn btn-primary">Buat Tiket Baru</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <p>Total Tiket: {{ $tikets->total() }}</p>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No. Tiket</th>
                            <th>Pemohon</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tikets as $tiket)
                            <tr>
                                <td>{{ $tiket->no_tiket }}</td>
                                <td>{{ $tiket->pemohon->name ?? '-' }}</td>
                                <td>{{ $tiket->layanan->nama ?? '-' }}</td>
                                <td><span
                                        class="badge bg-label-secondary">{{ $tiket->statusTerbaru->status ?? 'Draft' }}</span>
                                </td>
                                <td>
                                    {{-- Menggunakan route name yang baru --}}
                                    <a href="{{ route('ticket.edit', $tiket->id) }}" class="btn btn-sm btn-info">Detail &
                                        Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $tikets->links() }}
            </div>
        </div>
    </div>
@endsection
