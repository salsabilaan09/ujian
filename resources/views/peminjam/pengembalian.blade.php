@extends('layouts.app')

@section('title', 'Pengembalian Alat - Panel Peminjam')
@section('header-title', 'Pengembalian Alat')

@section('content')
    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="mb-5 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
        <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0z"></path></svg>
        <p><span class="font-bold">Informasi:</span> Serahkan alat kepada Petugas untuk diperiksa dan dicatat.</p>
    </div>

    @forelse($peminjaman as $item)
        <article class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-bold text-slate-900">Peminjaman #{{ $item->id }}</h2>
                        @if($item->pengembalian?->status === 'diajukan')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Menunggu Pemeriksaan</span>
                        @elseif($item->status === 'telat')
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Terlambat</span>
                        @else
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Sedang Dipinjam</span>
                        @endif
                    </div>
                    <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm text-slate-500">
                        <p>Tanggal Pinjam: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d-m-Y') }}</span></p>
                        <p>Rencana Kembali: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d-m-Y') }}</span></p>
                    </div>
                </div>
                @if(!$item->pengembalian)
                    <form action="{{ route('peminjam.pengembalian.ajukan', $item->id) }}" method="POST" onsubmit="return confirm('Ajukan pengembalian untuk peminjaman ini?')">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 sm:w-auto">Ajukan Pengembalian</button>
                    </form>
                @else
                    <p class="rounded-lg bg-slate-100 px-4 py-2.5 text-center text-sm font-medium text-slate-600">Pengajuan telah diterima petugas</p>
                @endif
            </div>

            <div class="p-5">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-500">Detail Alat</h3>
                <div class="divide-y divide-slate-100 rounded-xl border border-slate-200">
                    @foreach($item->detailPinjam as $detail)
                        <div class="flex flex-col gap-1 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }}</p>
                                <p class="text-sm text-slate-500">{{ $detail->alat->kategori->nama_kategori ?? 'Tanpa kategori' }}</p>
                            </div>
                            <span class="text-sm font-medium text-slate-700">{{ $detail->jumlah }} unit</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    @empty
        <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <h2 class="mt-4 text-lg font-bold text-slate-900">Tidak ada alat yang sedang dipinjam</h2>
            <p class="mt-1 text-sm text-slate-500">Semua pengembalian Anda sudah selesai atau belum ada pinjaman aktif.</p>
            <a href="{{ route('peminjam.katalog') }}" class="mt-5 inline-flex rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Lihat Katalog Alat</a>
        </section>
    @endforelse
@endsection