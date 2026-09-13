<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - Peminjam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Panel Peminjam</a>
            <div class="d-flex">
                <a href="{{ route('peminjam.riwayat') }}" class="btn btn-outline-light btn-sm me-2">Riwayat Pinjam</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm text-primary">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Header & Form Search -->
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h3 class="text-lg font-bold text-gray-800">Mencari Katalog Alat</h3>
            <form action="{{ route('peminjam.katalog') }}" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('peminjam.katalog') }}"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h3 class="mb-3">Katalog Alat Tersedia</h3>
        
        <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
            @csrf
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Rencana Tanggal Kembali</label>
                        <input type="date" name="tgl_kembali_plan" class="form-control" required>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="50">Pilih</th>
                                <th>Nama Alat</th>
                                <th>Kategori</th>
                                <th>Stok Tersedia</th>
                                <th width="150">Jumlah Pinjam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alats as $index => $alat)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" class="form-check-input">
                                    </td>
                                    <td>{{ $alat->nama_alat }}</td>
                                    <td>{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                                    <td>{{ $alat->stok }}</td>
                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control form-control-sm" value="1" min="1" max="{{ $alat->stok }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada alat yang tersedia saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>