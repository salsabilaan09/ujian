<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        DB::transaction(function () use ($id) {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($peminjaman->status !== 'diajukan') {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Pengajuan ini sudah diproses sebelumnya.',
                ]);
            }

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();
                if ($alat->stok < $detail->jumlah) {
                    throw ValidationException::withMessages([
                        'stok' => "Stok {$alat->nama_alat} tidak mencukupi untuk menyetujui pengajuan ini.",
                    ]);
                }
            }

            foreach ($peminjaman->detailPinjam as $detail) {
                Alat::whereKey($detail->alat_id)->decrement('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'dipinjam']);
        });

        return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
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
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])->findOrFail($id);

        if ($peminjaman->pengembalian && $peminjaman->pengembalian->status !== 'diajukan') {
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

        DB::transaction(function () use ($request, $peminjamanId) {
            $peminjaman = Peminjaman::with('detailPinjam')->lockForUpdate()->findOrFail($peminjamanId);
            $pengembalian = $peminjaman->pengembalian;

            if ($pengembalian && $pengembalian->status !== 'diajukan') {
                throw ValidationException::withMessages([
                    'pengembalian' => 'Pengembalian untuk peminjaman ini sudah diproses.',
                ]);
            }

            if (!in_array($peminjaman->status, ['dipinjam', 'telat'], true)) {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Peminjaman ini tidak dapat diproses sebagai pengembalian.',
                ]);
            }

            if ($pengembalian) {
                $pengembalian->update([
                    'tgl_kembali' => now()->toDateString(),
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda' => $request->denda,
                    'petugas_id' => auth()->id(),
                    'status' => 'diproses',
                ]);
            } else {
                Pengembalian::create([
                    'peminjaman_id' => $peminjaman->id,
                    'tgl_kembali' => now()->toDateString(),
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda' => $request->denda,
                    'petugas_id' => auth()->id(),
                    'status' => 'diproses',
                ]);
            }

            $peminjaman->update(['status' => 'dikembalikan']);

            foreach ($peminjaman->detailPinjam as $detail) {
                Alat::whereKey($detail->alat_id)->increment('stok', $detail->jumlah);
            }
        });

        return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
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
            'status' => ['nullable', 'string', 'in:diajukan,dipinjam,dikembalikan,telat'],
        ]);

        $laporan = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($validated['start_date'] ?? null, function ($query, $startDate) {
                $query->whereDate('tgl_pinjam', '>=', $startDate);
            })
            ->when($validated['end_date'] ?? null, function ($query, $endDate) {
                $query->whereDate('tgl_pinjam', '<=', $endDate);
            })
            ->when($validated['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.index', [
            'peminjaman' => $laporan,
            'startDate' => $validated['start_date'] ?? null,
            'endDate' => $validated['end_date'] ?? null,
            'status' => $validated['status'] ?? null,
        ]);
    }
}