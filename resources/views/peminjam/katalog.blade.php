@extends('layouts.app')

@section('title', 'Katalog Alat - Panel Peminjam')
@section('header-title', 'Katalog Alat')

@section('content')
    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="font-semibold">Pengajuan belum dapat diproses:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf
        <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <label for="tgl_kembali_plan" class="block text-sm font-semibold text-slate-800">Rencana Tanggal Kembali</label>
            <p class="mt-1 text-sm text-slate-500">Pilih tanggal pengembalian yang direncanakan sebelum memilih alat.</p>
            <input id="tgl_kembali_plan" name="tgl_kembali_plan" type="date" required min="{{ now()->addDay()->toDateString() }}" value="{{ old('tgl_kembali_plan') }}"
                class="mt-3 w-full max-w-sm rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-bold text-slate-900">Daftar Alat Tersedia</h2>
                <p class="mt-1 text-sm text-slate-500">Pilih satu atau lebih alat dan tentukan jumlah yang ingin dipinjam.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Pilih</th>
                            <th class="px-4 py-3 font-semibold">Foto Barang</th>
                            <th class="px-4 py-3 font-semibold">Nama Alat</th>
                            <th class="px-4 py-3 font-semibold">Kategori</th>
                            <th class="px-4 py-3 text-center font-semibold">Stok Tersedia</th>
                            <th class="px-4 py-3 font-semibold">Jumlah Pinjam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($alats as $alat)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" @checked(in_array($alat->id, old('alat_id', [])))
                                        aria-label="Pilih {{ $alat->nama_alat }}" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3">
                                    @if($alat->gambar)
                                        <img src="{{ asset('storage/' . $alat->gambar) }}" alt="Foto {{ $alat->nama_alat }}" class="h-14 w-16 rounded-lg border border-slate-200 object-cover">
                                    @else
                                        <div class="flex h-14 w-16 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-slate-400" aria-label="Foto tidak tersedia">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4-4a2 2 0 0 1 3 0l3 3m-1-1 1-1a2 2 0 0 1 3 0l3 3M5 20h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"></path></svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $alat->nama_alat }}</td>
                                <td class="px-4 py-3">{{ $alat->kategori->nama_kategori ?? 'Tanpa kategori' }}</td>
                                <td class="px-4 py-3 text-center font-medium">{{ $alat->stok }}</td>
                                <td class="px-4 py-3">
                                    <select name="jumlah[{{ $alat->id }}]" aria-label="Jumlah {{ $alat->nama_alat }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                        @for($jumlah = 1; $jumlah <= $alat->stok; $jumlah++)
                                            <option value="{{ $jumlah }}" @selected((int) old("jumlah.{$alat->id}", 1) === $jumlah)>{{ $jumlah }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <p class="mt-3 font-semibold text-slate-700">Belum ada alat yang tersedia</p>
                                    <p class="mt-1 text-sm text-slate-500">Silakan cek kembali beberapa saat lagi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($alats->isNotEmpty())
                <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">Stok akan diperiksa kembali saat petugas menyetujui pengajuan.</p>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Ajukan Peminjaman</button>
                </div>
            @endif
        </section>
    </form>
@endsection