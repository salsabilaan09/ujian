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

    {{-- Form Peminjaman --}}
    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="mb-5">
                    <div class="w-full max-w-sm">
                        <label for="tgl_kembali_plan" class="mb-2 block text-base font-semibold text-slate-700">Rencana Tanggal Kembali</label>
                        <input type="date" id="tgl_kembali_plan" name="tgl_kembali_plan" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                    </div>
                </div>

                <div class="table-responsive">
                    <h4 class="mb-3 text-lg font-bold text-slate-800">Katalog Alat Tersedia</h4>
                    <table class="table table-hover align-middle text-base w-full min-w-full border border-slate-300">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="text-center px-3 py-2 border border-slate-300">Pilih</th>
                                <th width="35%" class="px-3 py-2 border border-slate-300">Nama Alat</th>
                                <th width="25%" class="px-3 py-2 border border-slate-300">Kategori</th>
                                <th width="18%" class="text-center px-3 py-2 border border-slate-300">Stok Tersedia</th>
                                <th class="px-3 py-2 border border-slate-300" width="125">Jumlah Pinjam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alats as $index => $alat)
                                <tr>
                                    <td class="text-center px-3 py-2 border border-slate-300">
                                        <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" class="form-check-input">
                                    </td>
                                    <td class="fw-semibold px-3 py-2 border border-slate-300">{{ $alat->nama_alat }}</td>
                                    <td class="px-3 py-2 border border-slate-300">
                                        <span class="badge bg-secondary text-base">
                                            {{ $alat->kategori->nama_kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center px-3 py-2 border border-slate-300">
                                        <span class="badge bg-info text-dark text-base">{{ $alat->stok }}</span>
                                    </td>
                                    <td class="px-3 py-2 border border-slate-300">
                                            <select name="jumlah[]" class="form-select form-select-sm text-base border-primary bg-primary-subtle text-primary fw-semibold" aria-label="Jumlah pinjam {{ $alat->nama_alat }}">
                                            @for($jumlah = 1; $jumlah <= $alat->stok; $jumlah++)
                                                <option value="{{ $jumlah }}">{{ $jumlah }}</option>
                                            @endfor
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center px-3 py-4 text-muted border border-slate-300">
                                        Tidak ada alat yang tersedia saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2.5 text-base font-semibold text-white shadow-sm transition-all duration-200 hover:bg-blue-700 hover:scale-105 active:scale-95 hover:shadow-lg hover:shadow-blue-500/20 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Ajukan Peminjaman
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection