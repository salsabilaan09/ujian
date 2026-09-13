@extends('layouts.app')

@section('content')
    <h1>Riwayat Peminjaman</h1>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
            <!-- Header & Form Search -->
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <form action="{{ route('peminjam.riwayat') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari riwayat peminjam..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('peminjam.riwayat') }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        @forelse($peminjaman as $item)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                
                <div class="p-5">
                    <p class="px-4 py-1.5"><strong>Peminjaman ID:</strong> {{ $item->id }}</p>
                    <p class="px-4 py-1.5"><strong>Tanggal Pinjam:</strong> {{ $item->tgl_pinjam }}</p>
                    <p class="px-4 py-1.5"><strong>Rencana Tanggal Kembali:</strong> {{ $item->tgl_kembali_plan }}</p>
                    <p class="px-4 py-1.5"><strong>Status:</strong> {{ $item->status }}</p>
                    <p class="px-4 py-1.5"><strong>Alat yang Dipinjam:</strong></p>
                    <ul class="list-disc list-inside">
                        @foreach($item->detailPinjam as $detail)
                            <li>{{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} pcs)</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
                Belum ada riwayat peminjaman.
            </div>
        @endforelse
@endsection