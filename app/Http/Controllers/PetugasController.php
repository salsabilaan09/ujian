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
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->latest()->paginate(5);
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
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->latest()
            ->paginate(5);

        foreach ($peminjaman as $pinjam) {
            if ($pinjam->status === 'dipinjam' && now()->startOfDay()->gt($pinjam->tgl_kembali_plan)) {
                $pinjam->update(['status' => 'telat']);
            }
        }

        return view('petugas.pengembalian.index', compact('peminjaman'));
    }

    public function formPengembalian($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);

        if ($peminjaman->pengembalian()->exists()) {
            return redirect()->route('petugas.pengembalian.index')
                ->with('error', 'Pengembalian untuk peminjaman ini sudah tercatat.');
        }

        if (!in_array($peminjaman->status, ['dipinjam', 'telat'], true)) {
            return redirect()->route('petugas.pengembalian.index')
                ->with('error', 'Peminjaman ini tidak dapat diproses sebagai pengembalian.');
        }

        return view('petugas.pengembalian.create', compact('peminjaman'));
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat,Hilang',
            'denda' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // FIX 1: Menggunakan 'detailPinjam' (tanpa akhiran 's')
            $peminjaman = Peminjaman::with('detailPinjam')->lockForUpdate()->findOrFail($peminjamanId);

            if ($peminjaman->pengembalian()->exists()) {
                DB::rollBack();
                return redirect()->route('petugas.pengembalian.index')
                    ->with('error', 'Pengembalian untuk peminjaman ini sudah tercatat.');
            }

            if (!in_array($peminjaman->status, ['dipinjam', 'telat'], true)) {
                throw new \RuntimeException('Peminjaman ini tidak dapat diproses sebagai pengembalian.');
            }

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

    public function indexLaporan(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $laporan = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($validated['start_date'] ?? null, function ($query, $startDate) {
                $query->whereDate('tgl_pinjam', '>=', $startDate);
            })
            ->when($validated['end_date'] ?? null, function ($query, $endDate) {
                $query->whereDate('tgl_pinjam', '<=', $endDate);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.index', [
            'peminjaman' => $laporan,
            'startDate' => $validated['start_date'] ?? null,
            'endDate' => $validated['end_date'] ?? null,
        ]);
    }
}