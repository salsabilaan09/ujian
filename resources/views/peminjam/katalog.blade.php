@extends('layouts.app')

@section('title', 'Katalog Alat - Dashboard Peminjam')
@section('header-title', 'Daftar Katalog Alat')

@section('content')
<div class="container-fluid py-3">
    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Sub Header & Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark">Katalog Alat Tersedia</h4>
        <a href="{{ route('peminjam.riwayat') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-clock-history me-1"></i> Riwayat Pinjam
        </a>
    </div>

    {{-- Form Peminjaman --}}
    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Rencana Tanggal Kembali</label>
                        <input type="date" name="tgl_kembali_plan" class="form-control" required>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="text-center">Pilih</th>
                                <th>Nama Alat</th>
                                <th>Kategori</th>
                                <th class="text-center" width="130">Stok Tersedia</th>
                                <th width="160">Jumlah Pinjam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alats as $index => $alat)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" class="form-check-input">
                                    </td>
                                    <td class="fw-semibold">{{ $alat->nama_alat }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $alat->kategori->nama_kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark fs-6">{{ $alat->stok }}</span>
                                    </td>
                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control form-control-sm" value="1" min="1" max="{{ $alat->stok }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Tidak ada alat yang tersedia saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        Ajukan Peminjaman
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection