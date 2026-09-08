@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header Tabel & Pencarian -->
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Daftar Pengguna Sistem</h3>
            <p class="text-xs text-gray-500">Kelola semua data pengguna dan hak akses aplikasi.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3">
            <!-- Form Pencarian -->
            <form action="{{ route('admin.user.index') }}" method="GET" class="flex w-full sm:w-auto items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, role..." 
                       class="w-full sm:w-64 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                
                <!-- Tombol Cari (Interaktif) -->
                <button type="submit" 
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-md">
                    Cari
                </button>
                
                <!-- Tombol Reset (Interaktif) -->
                @if(request('search'))
                    <a href="{{ route('admin.user.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200 hover:scale-105 active:scale-95">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Tombol Tambah User (Interaktif) -->
            <a href="{{ route('admin.user.create') }}" 
               class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-lg hover:shadow-blue-500/20 text-center whitespace-nowrap">
                + Tambah User
            </a>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200 text-gray-600 text-xs uppercase tracking-wider">
                    <th class="py-3 px-4 font-semibold">Nama</th>
                    <th class="py-3 px-4 font-semibold">Email</th>
                    <th class="py-3 px-4 font-semibold">Role / Hak Akses</th>
                    <th class="py-3 px-4 font-semibold">No. HP</th>
                    <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700 text-sm">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $user->email }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full 
                                @if($user->role === 'admin') bg-purple-100 text-purple-800
                                @elseif($user->role === 'petugas') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $user->no_hp ?? '-' }}</td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Tombol Edit (Interaktif) -->
                                <a href="{{ route('admin.user.edit', $user->id) }}" 
                                   class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow-sm transition-all duration-200 hover:scale-110 active:scale-90 hover:shadow-md hover:shadow-amber-500/20">
                                    Edit
                                </a>

                                <!-- Tombol Hapus (Interaktif) -->
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow-sm transition-all duration-200 hover:scale-110 active:scale-90 hover:shadow-md hover:shadow-red-500/20">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            Belum ada data pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($users, 'links'))
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection