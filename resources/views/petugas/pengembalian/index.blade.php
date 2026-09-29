@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Data & Transaksi Pengembalian Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Daftar Pengembalian Alat</h3>
            <p class="mt-1 text-sm text-slate-500">Pinjaman aktif dan terlambat yang menunggu pemeriksaan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">Peminjam</th>
                        <th class="py-3 px-4 font-semibold">Alat</th>
                        <th class="py-3 px-4 font-semibold">Tanggal Pinjam</th>
                        <th class="py-3 px-4 font-semibold">Rencana Kembali</th>
                        <th class="py-3 px-4 font-semibold">Keterlambatan</th>
                        <th class="py-3 px-4 font-semibold">Estimasi Denda</th>
                        <th class="py-3 px-4 font-semibold">Status</th>
                        <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($peminjaman as $pinjam)
                        @php
                            $tanggalRencana = \Carbon\Carbon::parse($pinjam->tgl_kembali_plan);
                            $hariTerlambat = \Carbon\Carbon::today()->gt($tanggalRencana)
                                ? $tanggalRencana->diffInDays(\Carbon\Carbon::today())
                                : 0;
                            $estimasiDenda = $hariTerlambat * 5000;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-4 px-4">
                                <div class="font-semibold text-slate-900">{{ $pinjam->user->name ?? 'User Dihapus' }}</div>
                                <div class="text-xs text-slate-500">{{ $pinjam->user->email ?? '' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                @foreach($pinjam->detailPinjam as $detail)
                                    <div>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} <span class="text-xs text-slate-500">({{ $detail->jumlah }} pcs)</span></div>
                                @endforeach
                            </td>
                            <td class="py-4 px-4 text-sm">{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d-m-Y') }}</td>
                            <td class="py-4 px-4 text-sm">{{ $tanggalRencana->format('d-m-Y') }}</td>
                            <td class="py-4 px-4">
                                <span class="font-semibold {{ $hariTerlambat > 0 ? 'text-red-600' : 'text-slate-500' }}">{{ $hariTerlambat }} hari</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-semibold {{ $estimasiDenda > 0 ? 'text-red-600' : 'text-emerald-600' }}">Rp {{ number_format($estimasiDenda, 0, ',', '.') }}</span>
                                <div class="text-xs text-slate-500">Rp 5.000 per hari terlambat</div>
                            </td>
                            <td class="py-4 px-4">
                                @if($pinjam->pengembalian)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Pengembalian Tercatat</span>
                                @elseif($pinjam->status === 'telat')
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">Terlambat</span>
                                @else
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">Dipinjam</span>
                                @endif
                                @if($pinjam->pengembalian)
                                    <div class="mt-1 text-xs text-slate-600">{{ $pinjam->pengembalian->kondisi_kembali }} · Rp {{ number_format($pinjam->pengembalian->denda, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($pinjam->pengembalian)
                                    <span class="text-xs text-slate-500">Sudah diproses</span>
                                @else
                                    <a href="{{ route('petugas.pengembalian.form', $pinjam->id) }}" class="inline-flex whitespace-nowrap rounded-md bg-emerald-700 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-800">Proses Pengembalian</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-500 text-sm">
                                Tidak ada peminjaman yang perlu dikembalikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($peminjaman->hasPages())
            <div class="px-4 py-4 border-t border-slate-200">
                {{ $peminjaman->links() }}
            </div>
        @endif
    </div>
@endsection