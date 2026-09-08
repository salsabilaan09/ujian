@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')
    <!-- Alert Selamat Datang -->
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang, <strong class="font-semibold">{{ auth()->user()->name }}</strong>! Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-900">{{ auth()->user()->role }}</span>.
    </div>

    <!-- Container Log Aktivitas (Timeline Style) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        
        <!-- Header & Form Search -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Log Aktivitas Terbaru</h3>
                <p class="text-xs text-gray-500 mt-0.5">Riwayat aktivitas pengguna dalam sistem</p>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <form action="{{ route('admin.dashboard') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user / aktivitas..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    
                    <button type="submit" 
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-md">
                        Cari
                    </button>

                    @if(request('search'))
                        <a href="{{ route('admin.dashboard') }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center shadow-sm transition-all duration-200 hover:scale-105 active:scale-95">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Tampilan Timeline Log -->
        <div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200">
            @forelse($logs as $log)
                @php
                    $role = strtolower($log->user->role ?? 'sistem');
                    
                    // Menentukan warna Badge dan Dot berdasarkan Role
                    switch($role) {
                        case 'admin':
                            $badgeClass = 'bg-purple-100 text-purple-700';
                            $dotClass = 'bg-purple-500 group-hover:bg-purple-600';
                            break;
                        case 'petugas':
                            $badgeClass = 'bg-blue-100 text-blue-700';
                            $dotClass = 'bg-blue-500 group-hover:bg-blue-600';
                            break;
                        case 'peminjam':
                        case 'user':
                            $badgeClass = 'bg-emerald-100 text-emerald-700';
                            $dotClass = 'bg-emerald-500 group-hover:bg-emerald-600';
                            break;
                        default:
                            $badgeClass = 'bg-gray-100 text-gray-600';
                            $dotClass = 'bg-gray-400 group-hover:bg-gray-500';
                    }
                @endphp

                <div class="relative flex items-start group">
                    <!-- Dot Indikator (Warna Sesuai Role) -->
                    <div class="absolute -left-6 top-2 w-3 h-3 rounded-full {{ $dotClass }} ring-4 ring-white group-hover:scale-125 transition-all duration-200"></div>

                    <!-- Card Isi Log -->
                    <div class="flex-1 bg-gray-50 hover:bg-slate-100/80 p-4 rounded-xl border border-gray-100 transition-all duration-200 hover:shadow-sm">
                        <div class="flex flex-wrap justify-between items-center gap-2 mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 text-sm">
                                    {{ $log->user->name ?? 'Sistem' }}
                                </span>
                                @if(isset($log->user->role))
                                    <!-- Badge Role (Warna Sesuai Foto) -->
                                    <span class="text-xs px-3 py-0.5 rounded-full font-medium capitalize {{ $badgeClass }}">
                                        {{ $log->user->role }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }} 
                                <span class="text-gray-300 mx-1">•</span> 
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            {{ $log->aktivitas }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 text-sm">
                    Belum ada log aktivitas.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6 pt-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>
@endsection