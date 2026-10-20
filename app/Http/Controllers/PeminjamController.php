<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();
        $menungguPersetujuan = Peminjaman::where('user_id', $userId)
            ->where('status', 'diajukan')
            ->count();
        $sedangDipinjam = Peminjaman::where('user_id', $userId)
            ->whereIn('status', ['dipinjam', 'telat'])
            ->count();
        $alatTersedia = Alat::sum('stok');

        return view('peminjam.dashboard', compact(
            'totalPeminjaman',
            'menungguPersetujuan',
            'sedangDipinjam',
            'alatTersedia'
        ));
    }

    public function katalogAlat()
    {
        $alats = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->orderBy('nama_alat')
            ->get();

        return view('peminjam.katalog', compact('alats'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $validated = $request->validate([
            'tgl_kembali_plan' => ['required', 'date', 'after:today'],
            'alat_id' => ['required', 'array', 'min:1'],
            'alat_id.*' => ['required', 'integer', 'distinct', 'exists:alat,id'],
            'jumlah' => ['required', 'array'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
        ], [
            'alat_id.required' => 'Pilih setidaknya satu alat yang ingin dipinjam.',
            'alat_id.min' => 'Pilih setidaknya satu alat yang ingin dipinjam.',
            'tgl_kembali_plan.after' => 'Tanggal kembali harus setelah hari ini.',
        ]);

        DB::transaction(function () use ($validated) {
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now()->toDateString(),
                'tgl_kembali_plan' => $validated['tgl_kembali_plan'],
                'status' => 'diajukan',
            ]);

            foreach ($validated['alat_id'] as $alatId) {
                $jumlah = $validated['jumlah'][$alatId] ?? null;
                if (! is_numeric($jumlah) || (int) $jumlah < 1) {
                    throw ValidationException::withMessages([
                        "jumlah.{$alatId}" => 'Jumlah pinjam minimal satu.',
                    ]);
                }

                $alat = Alat::whereKey($alatId)->lockForUpdate()->firstOrFail();
                if ($alat->stok < (int) $jumlah) {
                    throw ValidationException::withMessages([
                        'alat_id' => "Stok {$alat->nama_alat} tidak mencukupi.",
                    ]);
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alat->id,
                    'jumlah' => (int) $jumlah,
                ]);
            }
        });

        return redirect()->route('peminjam.riwayat')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim dan menunggu persetujuan petugas.');
    }

    public function riwayatPeminjaman()
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjaman'));
    }

    public function indexPengembalian()
    {
        $peminjaman = Peminjaman::with(['detailPinjam.alat.kategori', 'pengembalian'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['dipinjam', 'telat'])
            ->latest()
            ->get();

        return view('peminjam.pengembalian', compact('peminjaman'));
    }

    public function ajukanPengembalian(int $id)
    {
        DB::transaction(function () use ($id) {
            $peminjaman = Peminjaman::whereKey($id)
                ->where('user_id', auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($peminjaman->status, ['dipinjam', 'telat'], true)) {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Pinjaman ini tidak sedang aktif dan tidak dapat diajukan untuk dikembalikan.',
                ]);
            }

            if ($peminjaman->pengembalian()->exists()) {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Pengajuan pengembalian untuk pinjaman ini sudah dikirim.',
                ]);
            }

            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now()->toDateString(),
                'kondisi_kembali' => 'Menunggu pemeriksaan petugas',
                'denda' => 0,
                'petugas_id' => null,
                'status' => 'diajukan',
            ]);
        });

        return redirect()->route('peminjam.pengembalian.index')
            ->with('success', 'Pengajuan pengembalian berhasil dikirim. Serahkan alat kepada petugas untuk diperiksa.');
    }
}
