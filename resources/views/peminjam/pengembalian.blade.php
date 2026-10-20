@extends ('layouts.app') 

@section('content')
    <div class="container-fluid py-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3 text-lg font-bold text-slate-800">Pengembalian Alat</h4>
                <form action="{{ route('pengembalian.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">
                    <div class="mb-4">
                        <label for="tgl_kembali" class="mb-2 block text-base font-semibold text-slate-700">Tanggal Kembali</label>
                        <input type="date" id="tgl_kembali" name="tgl_kembali" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                    </div>
                    <div class="mb-4">
                        <label for="foto_bukti" class="mb-2 block text-base font-semibold text-slate-700">Foto Bukti Pengembalian</label>
                        <input type="file" id="foto_bukti" name="foto_bukti" accept=".jpg,.jpeg,.png,.pdf" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                    </div>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white shadow-sm transition-all duration-200 hover:bg-blue-700 hover:scale-105 active:scale-95 hover:shadow-md">Kirim Pengembalian</button>
                </form>
            </div>
        </div>
    </div>
@endsection