<?php

namespace App\Http\Controllers;

use App\Models\BobotPenilaian;
use App\Models\MataKuliah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BobotPenilaianController extends Controller
{
    /**
     * Tampilkan daftar Bobot Penilaian.
     */
    public function index()
    {
        $data = BobotPenilaian::orderBy('id_mk', 'asc')
            ->orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        // Untuk dropdown CPMK di modal (ambil mapping CPL-CPMK-MK)
        // Gunakan guard agar halaman tetap jalan saat tabel belum ada.
        if (Schema::hasTable('tbl_pemetaan_cpl_cpmk_mk')) {
            $cpmks = DB::table('tbl_pemetaan_cpl_cpmk_mk')
                ->orderBy('id_cpl', 'asc')
                ->orderBy('id_mk', 'asc')
                ->orderBy('id_cpmk', 'asc')
                ->get();
        } else {
            $cpmks = collect([]);
        }

        // Untuk dropdown Mata Kuliah
        $mataKuliahs = MataKuliah::orderBy('nama_mk', 'asc')->get();

        return view('bobot-penilaian', compact('data', 'cpmks', 'mataKuliahs'));
    }

    /**
     * Simpan data baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_mk' => 'required|string|max:10',
            'id_cpl' => 'required|string|max:10',
            'id_cpmk' => 'required|string|max:10',
            'mbkm' => 'nullable|string|max:255',
            'partisipasi' => 'nullable|numeric|min:0|max:100',
            'observasi' => 'nullable|numeric|min:0|max:100',
            'unjuk_kerja' => 'nullable|numeric|min:0|max:100',
            'tes_uts' => 'nullable|numeric|min:0|max:100',
            'tes_uas' => 'nullable|numeric|min:0|max:100',
            'tes_lisan' => 'nullable|numeric|min:0|max:100',
        ]);

        // Ambil nama_mk dari tabel mst_mk
        $mataKuliah = MataKuliah::where('id', $request->id_mk)->first();
        $namaMk = $mataKuliah ? $mataKuliah->nama_mk : '';

        // Hitung total dari semua nilai
        $total = ($request->partisipasi ?? 0) +
                 ($request->observasi ?? 0) +
                 ($request->unjuk_kerja ?? 0) +
                 ($request->tes_uts ?? 0) +
                 ($request->tes_uas ?? 0) +
                 ($request->tes_lisan ?? 0);

        $data = $request->only([
            'id_mk',
            'id_cpl',
            'id_cpmk',
            'mbkm',
            'partisipasi',
            'observasi',
            'unjuk_kerja',
            'tes_uts',
            'tes_uas',
            'tes_lisan',
        ]);

        $data['nama_mk'] = $namaMk;
        $data['total'] = $total;

        // Simpan record baru
        $bobot = BobotPenilaian::create($data);

        // Recalculate total_nilai_mk sebagai total dari semua baris dengan id_mk yang sama
        $totalNilaiMk = BobotPenilaian::where('id_mk', $bobot->id_mk)
            ->sum('total');

        BobotPenilaian::where('id_mk', $bobot->id_mk)
            ->update(['total_nilai_mk' => $totalNilaiMk]);

        return redirect()->route('bobot-penilaian.index')->with('success', 'Data bobot penilaian berhasil ditambahkan!');
    }

    /**
     * Ambil satu record (untuk modal edit via AJAX).
     */
    public function show(BobotPenilaian $bobotPenilaian)
    {
        return response()->json($bobotPenilaian);
    }

    /**
     * Update data.
     */
    public function update(Request $request, BobotPenilaian $bobotPenilaian)
    {
        $request->validate([
            'mbkm' => 'nullable|string|max:255',
            'partisipasi' => 'nullable|numeric|min:0|max:100',
            'observasi' => 'nullable|numeric|min:0|max:100',
            'unjuk_kerja' => 'nullable|numeric|min:0|max:100',
            'tes_uts' => 'nullable|numeric|min:0|max:100',
            'tes_uas' => 'nullable|numeric|min:0|max:100',
            'tes_lisan' => 'nullable|numeric|min:0|max:100',
        ]);

        // Hitung total dari semua nilai
        $total = ($request->partisipasi ?? 0) +
                 ($request->observasi ?? 0) +
                 ($request->unjuk_kerja ?? 0) +
                 ($request->tes_uts ?? 0) +
                 ($request->tes_uas ?? 0) +
                 ($request->tes_lisan ?? 0);

        $updateData = $request->only([
            'mbkm',
            'partisipasi',
            'observasi',
            'unjuk_kerja',
            'tes_uts',
            'tes_uas',
            'tes_lisan',
        ]);

        $updateData['total'] = $total;

        // CPL/MK/CPMK dianggap tetap (tidak diedit dari halaman ini)
        $bobotPenilaian->update($updateData);

        // Recalculate total_nilai_mk untuk id_mk yang sama
        $totalNilaiMk = BobotPenilaian::where('id_mk', $bobotPenilaian->id_mk)
            ->sum('total');

        BobotPenilaian::where('id_mk', $bobotPenilaian->id_mk)
            ->update(['total_nilai_mk' => $totalNilaiMk]);

        return redirect()->route('bobot-penilaian.index')
            ->with('success', 'Data bobot penilaian berhasil diupdate!');
    }

    /**
     * Hapus satu data (route: DELETE /bobot-penilaian/{bobotPenilaian}).
     * Hanya baris yang dipilih saja yang dihapus, data lain tetap aman.
     */
    public function destroy(BobotPenilaian $bobotPenilaian)
    {
        try {
            // Simpan id_mk untuk recalculasi total_nilai_mk setelah delete
            $idMk = $bobotPenilaian->id_mk;

            // Hapus hanya satu baris ini
            $bobotPenilaian->delete();

            // Recalculate total_nilai_mk untuk id_mk yang sama
            $totalNilaiMk = BobotPenilaian::where('id_mk', $idMk)
                ->sum('total');

            // Update total_nilai_mk di semua baris yang masih tersisa untuk id_mk ini
            BobotPenilaian::where('id_mk', $idMk)
                ->update(['total_nilai_mk' => $totalNilaiMk]);

            return redirect()->route('bobot-penilaian.index')
                ->with('success', 'Satu data bobot penilaian berhasil dihapus!');
        } catch (QueryException $e) {
            return redirect()->route('bobot-penilaian.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus banyak data sekaligus (route: POST /bobot-penilaian/delete-multiple).
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tbl_bobot_penilaian,id',
        ]);

        try {
            // Ambil dulu id_mk dari baris yang akan dihapus
            $rows = BobotPenilaian::whereIn('id', $request->ids)
                ->get(['id_mk']);

            // Hapus baris-baris yang dipilih berdasarkan primary key "id"
            $deletedCount = BobotPenilaian::whereIn('id', $request->ids)->delete();

            // Untuk setiap id_mk unik, hitung ulang total_nilai_mk
            $uniqueMkIds = $rows->pluck('id_mk')->unique();

            foreach ($uniqueMkIds as $idMk) {
                $totalNilaiMk = BobotPenilaian::where('id_mk', $idMk)
                    ->sum('total');

                BobotPenilaian::where('id_mk', $idMk)
                    ->update(['total_nilai_mk' => $totalNilaiMk]);
            }

            return redirect()->route('bobot-penilaian.index')
                ->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            return redirect()->route('bobot-penilaian.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
