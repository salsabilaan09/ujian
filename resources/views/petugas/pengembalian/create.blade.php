@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Petugas')
@section('header-title', 'Pemeriksaan Pengembalian Alat')

@section('content')
    @php
        $tanggalRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
        $tanggalHariIni = \Carbon\Carbon::today();
        $hariTerlambat = $tanggalHariIni->gt($tanggalRencana)
            ? $tanggalRencana->diffInDays($tanggalHariIni)
            : 0;
        $estimasiDenda = $hariTerlambat * 5000;
    @endphp

    @if($errors->any())
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="max-w-3xl rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <h2 class="text-lg font-bold text-slate-900">Konfirmasi Pengembalian</h2>
            <p class="mt-1 text-sm text-slate-600">Periksa kondisi setiap alat dan tetapkan denda sebelum menyimpan.</p>
        </div>

        <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST" class="space-y-5 p-5">
            @csrf

            <section class="grid gap-4 sm:grid-cols-2">
                <div>
                    <h3 class="mb-1 text-sm font-semibold text-slate-700">Peminjam</h3>
                    <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                        <div class="font-medium text-slate-900">{{ $peminjaman->user->name ?? 'User Dihapus' }}</div>
                        <div class="text-xs text-slate-500">{{ $peminjaman->user->email ?? '' }}</div>
                    </div>
                </div>
                <div>
                    <h3 class="mb-1 text-sm font-semibold text-slate-700">Status Pinjaman</h3>
                    <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm">{{ ucfirst($peminjaman->status) }}</div>
                </div>
                <div>
                    <h3 class="mb-1 text-sm font-semibold text-slate-700">Tanggal Pinjam</h3>
                    <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm">{{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d-m-Y') }}</div>
                </div>
                <div>
                    <h3 class="mb-1 text-sm font-semibold text-slate-700">Rencana Kembali</h3>
                    <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm">{{ $tanggalRencana->format('d-m-Y') }}</div>
                </div>
            </section>

            <section>
                <h3 class="mb-2 text-sm font-semibold text-slate-700">Alat yang Dipinjam</h3>
                <div class="divide-y divide-slate-200 rounded-md border border-slate-200">
                    @foreach($peminjaman->detailPinjam as $detail)
                        <div class="flex items-center justify-between gap-4 px-4 py-3 text-sm">
                            <span class="font-medium text-slate-900">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                            <span class="text-slate-600">{{ $detail->jumlah }} pcs</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-md border {{ $hariTerlambat > 0 ? 'border-red-200 bg-red-50' : 'border-emerald-200 bg-emerald-50' }} p-4">
                <div class="flex justify-between gap-4 text-sm">
                    <span class="text-slate-700">Tanggal pengembalian</span>
                    <span class="font-semibold text-slate-900">{{ $tanggalHariIni->format('d-m-Y') }}</span>
                </div>
                <div class="mt-2 flex justify-between gap-4 text-sm">
                    <span class="text-slate-700">Keterlambatan</span>
                    <span class="font-semibold {{ $hariTerlambat > 0 ? 'text-red-700' : 'text-emerald-700' }}">{{ $hariTerlambat }} hari</span>
                </div>
                <div class="mt-2 flex justify-between gap-4 text-sm">
                    <span class="text-slate-700">Estimasi denda keterlambatan</span>
                    <span class="font-bold {{ $estimasiDenda > 0 ? 'text-red-700' : 'text-emerald-700' }}">Rp {{ number_format($estimasiDenda, 0, ',', '.') }}</span>
                </div>
                <p class="mt-2 text-xs text-slate-600">Perhitungan keterlambatan mengikuti Rp 5.000 per hari. Denda akhir dapat disesuaikan berdasarkan hasil pemeriksaan kondisi alat.</p>
            </section>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="kondisi_kembali" class="mb-1 block text-sm font-semibold text-slate-700">Kondisi Alat Saat Dikembalikan</label>
                    <select id="kondisi_kembali" name="kondisi_kembali" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-1 focus:ring-emerald-700">
                        <option value="">Pilih kondisi</option>
                        @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'] as $kondisi)
                            <option value="{{ $kondisi }}" @selected(old('kondisi_kembali') === $kondisi)>{{ $kondisi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="denda" class="mb-1 block text-sm font-semibold text-slate-700">Denda (Rp)</label>
                    <input id="denda" name="denda" type="number" min="0" step="1" required value="{{ old('denda', $estimasiDenda) }}" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-1 focus:ring-emerald-700">
                    <p class="mt-1 text-xs text-slate-500">Isi jumlah akhir berdasarkan keterlambatan dan kondisi alat.</p>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-slate-200 pt-4">
                <a href="{{ route('petugas.pengembalian.index') }}" class="rounded-md bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</a>
                <button type="submit" onclick="return confirm('Proses pengembalian dan simpan kondisi serta denda?')" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Konfirmasi Pengembalian</button>
            </div>
        </form>
    </div>
@endsection
