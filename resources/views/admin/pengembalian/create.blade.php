@extends('layouts.app')

@section('title', 'Pengembalian Alat - Panel Admin')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')

@php
    $tanggalRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
    $tanggalSekarang = \Carbon\Carbon::today();
    $hariTerlambat = $tanggalSekarang->gt($tanggalRencana)
        ? $tanggalRencana->diffInDays($tanggalSekarang)
        : 0;
    $estimasiDenda = $hariTerlambat * 5000;
@endphp

<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    {{-- Pesan Error --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800">
            Konfirmasi Pengembalian
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Pastikan data alat dan peminjam sudah sesuai sebelum diproses.
        </p>
    </div>

    {{-- Data Peminjam --}}
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">
            Peminjam
        </label>

        <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg">
            <div class="font-semibold text-gray-800">
                {{ $peminjaman->user->name ?? 'User Dihapus' }}
            </div>

            @if($peminjaman->user)
                <div class="text-xs text-gray-500">
                    {{ $peminjaman->user->email }}
                </div>
            @endif
        </div>
    </div>

    {{-- Tanggal --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Tanggal Pinjam
            </label>

            <div class="px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm">
                {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d-m-Y') }}
            </div>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Rencana Kembali
            </label>

            <div class="px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm">
                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d-m-Y') }}
            </div>
        </div>

    </div>

    {{-- Alat --}}
    <div class="mb-4">

        <label class="block text-gray-700 text-sm font-semibold mb-2">
            Alat yang Dipinjam
        </label>

        <div class="border border-gray-200 rounded-lg overflow-hidden">

            @foreach($peminjaman->detailPinjam as $detail)

                <div class="flex justify-between items-center px-4 py-3 border-b last:border-b-0">

                    <div>
                        <div class="font-semibold text-gray-800">
                            {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                        </div>

                        <div class="text-xs text-gray-500">
                            Jumlah dipinjam: {{ $detail->jumlah }} pcs
                        </div>
                    </div>

                    <div class="text-sm font-semibold text-gray-700">
                        {{ $detail->jumlah }} pcs
                    </div>

                </div>

            @endforeach

        </div>

    </div>

    {{-- Perhitungan Denda --}}
    <div class="mb-6">

        <label class="block text-gray-700 text-sm font-semibold mb-2">
            Informasi Pengembalian
        </label>

        <div class="border rounded-lg p-4
            {{ $hariTerlambat > 0
                ? 'bg-red-50 border-red-200'
                : 'bg-emerald-50 border-emerald-200' }}">

            <div class="flex justify-between text-sm mb-2">

                <span class="text-gray-600">
                    Tanggal Pengembalian
                </span>

                <span class="font-semibold text-gray-800">
                    {{ $tanggalSekarang->format('d-m-Y') }}
                </span>

            </div>

            <div class="flex justify-between text-sm mb-2">

                <span class="text-gray-600">
                    Keterlambatan
                </span>

                <span class="font-semibold
                    {{ $hariTerlambat > 0 ? 'text-red-600' : 'text-emerald-600' }}">

                    {{ $hariTerlambat }} hari

                </span>

            </div>

            <div class="flex justify-between text-sm">

                <span class="text-gray-600">
                    Denda
                </span>

                <span class="font-bold text-lg
                    {{ $estimasiDenda > 0 ? 'text-red-600' : 'text-emerald-600' }}">

                    Rp {{ number_format($estimasiDenda, 0, ',', '.') }}

                </span>

            </div>

            @if($hariTerlambat > 0)

                <div class="mt-3 text-xs text-red-700">
                    Terlambat {{ $hariTerlambat }} hari.
                    Estimasi denda keterlambatan dihitung Rp 5.000 per hari.
                </div>

            @else

                <div class="mt-3 text-xs text-emerald-700">
                    Pengembalian tepat waktu. Tidak ada denda.
                </div>

            @endif

        </div>

    </div>

    <form id="admin-return-form" action="{{ route('admin.pengembalian.kembalikan', $peminjaman->id) }}" method="POST" class="mb-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="kondisi_kembali" class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Alat Saat Dikembalikan</label>
                <select id="kondisi_kembali" name="kondisi_kembali" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Pilih kondisi</option>
                    @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'] as $kondisi)
                        <option value="{{ $kondisi }}" @selected(old('kondisi_kembali') === $kondisi)>{{ $kondisi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="denda" class="block text-gray-700 text-sm font-semibold mb-2">Denda (Rp)</label>
                <input id="denda" name="denda" type="number" min="0" step="1" required value="{{ old('denda', $estimasiDenda) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <p class="mt-1 text-xs text-gray-500">Nominal awal mengikuti keterlambatan; sesuaikan berdasarkan kondisi alat.</p>
            </div>
        </div>
    </form>

    {{-- Tombol --}}
    <div class="flex justify-end space-x-2">

        <a href="{{ route('admin.pengembalian.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">

            Batal

        </a>

        <button type="submit" form="admin-return-form" onclick="return confirm('Yakin ingin memproses pengembalian alat ini?')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            Konfirmasi Pengembalian
        </button>

    </div>

</div>

@endsection