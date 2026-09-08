@extends('layouts.app')

@section('title', 'Kelola Kategori - Panel Admin')
@section('header-title', 'Manajemen Kategori Alat')

@section('content')
    <!-- Notifikasi -->
    
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Daftar Kategori Alat</h3>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Form Search -->
                <form action="{{ route('admin.kategori.index') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    
                    <!-- Tombol Cari (Interaktif) -->
                    <button type="submit" 
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-md">
                        Cari
                    </button>

                    <!-- Tombol Reset (Interaktif) -->
                    @if(request('search'))
                        <a href="{{ route('admin.kategori.index') }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center shadow-sm transition-all duration-200 hover:scale-105 active:scale-95">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Tombol Tambah (Interaktif) -->
                <a href="{{ route('admin.kategori.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-lg hover:shadow-blue-500/20 whitespace-nowrap">
                    + Tambah Kategori
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b w-16 text-center">No</th>
                        <th class="py-3 px-4 border-b">Nama Kategori</th>
                        <th class="py-3 px-4 border-b w-48 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($kategori as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 border-b text-center">{{ $kategori->firstItem() + $index }}</td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $item->nama_kategori }}</td>
                            <td class="py-3 px-4 border-b text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Tombol Edit (Interaktif) -->
                                    <a href="{{ route('admin.kategori.edit', $item->id) }}"
                                       class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-md hover:shadow-amber-500/20">
                                        Edit
                                    </a>

                                    <!-- Tombol Hapus (Interaktif) -->
                                    <form action="{{ route('admin.kategori.destroy', $item->id) }}"
                                          method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-md hover:shadow-red-500/20">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $kategori->links() }}
        </div>
    </div>
@endsection