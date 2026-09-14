<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman()
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->latest()->get();
        return view('petugas.peminjaman.index', compact('peminjaman'));
    }

    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indexPengembalian()
    {
        // Mengambil data yang statusnya 'dipinjam' atau 'dikembalikan'
        $pengembalian = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['dipinjam', 'dikembalikan'])
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', [
            'peminjaman' => $pengembalian
        ]);
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            // FIX 1: Menggunakan 'detailPinjam' (tanpa akhiran 's')
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            // FIX 2: Mengubah status dari 'selesai' menjadi 'dikembalikan'
            $peminjaman->update(['status' => 'dikembalikan']);

            // Kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak');
            } 

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indexLaporan()
    {
        $laporan = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->latest()
            ->get();

        return view('petugas.laporan.index', [
            'peminjaman' => $laporan
        ]);
    }
}