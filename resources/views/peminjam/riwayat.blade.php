@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Dashboard Peminjam')
@section('header-title', 'Riwayat Peminjaman')

@section('content')
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 p-5 md:flex-row md:items-center md:justify-between">
            <h1 class="text-lg font-bold text-gray-800">Riwayat Peminjaman</h1>
            <form action="{{ route('peminjam.riwayat') }}" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari riwayat peminjaman..."
                    class="w-full rounded-l-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="rounded-r-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-gray-900 hover:scale-105 active:scale-95 hover:shadow-md">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('peminjam.riwayat') }}"
                        class="ml-2 flex items-center rounded-lg bg-gray-300 px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-400">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto p-4">
            <table class="w-full min-w-[720px] border-collapse text-left text-sm text-gray-700">
                <thead>
                    <tr class="bg-gray-100 text-xs uppercase tracking-wide text-gray-600">
                        <th class="border border-gray-200 px-4 py-3">ID</th>
                        <th class="border border-gray-200 px-4 py-3">Tanggal Pinjam</th>
                        <th class="border border-gray-200 px-4 py-3">Rencana Kembali</th>
                        <th class="border border-gray-200 px-4 py-3">Alat Dipinjam</th>
                        <th class="border border-gray-200 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjaman as $item)
                        <tr class="transition hover:bg-blue-50/40">
                            <td class="border border-gray-200 px-4 py-3 font-semibold text-gray-900">#{{ $item->id }}</td>
                            <td class="border border-gray-200 px-4 py-3">{{ $item->tgl_pinjam }}</td>
                            <td class="border border-gray-200 px-4 py-3">{{ $item->tgl_kembali_plan }}</td>
                            <td class="border border-gray-200 px-4 py-3">
                                <ul class="space-y-1">
                                    @foreach($item->detailPinjam as $detail)
                                        <li>{{ $detail->alat->nama_alat ?? 'Alat' }} <span class="text-gray-500">({{ $detail->jumlah }} pcs)</span></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="border border-gray-200 px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                    @if($item->status == 'diajukan') bg-yellow-100 text-yellow-800
                                    @elseif($item->status == 'dipinjam') bg-blue-100 text-blue-800
                                    @elseif($item->status == 'dikembalikan') bg-emerald-100 text-emerald-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-gray-200 px-4 py-8 text-center text-gray-500">
                                Belum ada riwayat peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection