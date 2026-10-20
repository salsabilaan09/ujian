@extends('layouts.app')

@section('title', 'Dashboard Peminjam')
@section('header-title', 'Dashboard Peminjam')

@section('content')
    <div class="mb-6 rounded-2xl bg-gradient-to-r from-slate-900 to-indigo-900 p-6 text-white shadow-sm sm:p-8">
        <p class="text-sm font-medium text-indigo-200">Panel Peminjam</p>
        <h2 class="mt-2 text-2xl font-bold sm:text-3xl">Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="mt-2 max-w-2xl text-sm text-slate-300">Kelola peminjaman alat, lihat status pengajuan, dan ajukan pengembalian dari satu tempat.</p>
        <a href="{{ route('peminjam.katalog') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-indigo-50">
            Lihat Katalog Alat
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7 7 7-7 7"></path></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Peminjaman', 'value' => $totalPeminjaman, 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2', 'color' => 'blue'],
            ['label' => 'Menunggu Persetujuan', 'value' => $menungguPersetujuan, 'icon' => 'M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', 'color' => 'amber'],
            ['label' => 'Sedang Dipinjam', 'value' => $sedangDipinjam, 'icon' => 'M5 13l4 4L19 7', 'color' => 'emerald'],
            ['label' => 'Alat Tersedia', 'value' => $alatTersedia, 'icon' => 'M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'violet'],
        ] as $stat)
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($stat['value']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl
                        @if($stat['color'] === 'blue') bg-blue-50 text-blue-600
                        @elseif($stat['color'] === 'amber') bg-amber-50 text-amber-600
                        @elseif($stat['color'] === 'emerald') bg-emerald-50 text-emerald-600
                        @else bg-violet-50 text-violet-600 @endif">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $stat['icon'] }}"></path></svg>
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h3 class="text-base font-bold text-slate-900">Mulai dari sini</h3>
        <p class="mt-1 text-sm text-slate-500">Ajukan alat yang Anda butuhkan atau cek pinjaman yang sedang berjalan.</p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('peminjam.katalog') }}" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Ajukan Peminjaman</a>
            <a href="{{ route('peminjam.pengembalian.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Lihat Pengembalian</a>
        </div>
    </section>
@endsection
