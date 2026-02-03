<?php

namespace App\Http\Controllers;

use App\Models\TahapMekanisme;
use App\Models\PemetaanCplCpmkMk;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TahapMekanismeController extends Controller
{
    /**
     * Tampilkan daftar Tahap Mekanisme Penilaian.
     */
    public function index()
    {
        $data = TahapMekanisme::orderBy('id_cpl', 'asc')
            ->orderBy('id_mk', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        // Untuk dropdown CPMK di modal (ambil mapping CPL–CPMK–MK)
        $cpmks = PemetaanCplCpmkMk::orderBy('id_cpl', 'asc')
            ->orderBy('id_mk', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        return view('tahap-mekanisme-penilaian', compact('data', 'cpmks'));
    }

    /**
     * Simpan data baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_cpl' => 'required|string|max:10',
            'id_mk' => 'required|string|max:10',
            'id_cpmk' => 'required|string|max:10',
            'tahap_penilaian' => 'required|string|max:255',
            'teknik_penilaian' => 'required|string|max:255',
            'instrumen' => 'required|string|max:255',
            'kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric',
        ]);

        // Hitung bobot_mk sebagai total bobot untuk kombinasi CPL+MK+CPMK yang sama
        $data = $request->only([
            'id_cpl',
            'id_mk',
            'id_cpmk',
            'tahap_penilaian',
            'teknik_penilaian',
            'instrumen',
            'kriteria',
            'bobot',
        ]);

        // Simpan record baru
        $tahap = TahapMekanisme::create($data);

        // Recalculate total bobot untuk kombinasi yang sama dan simpan ke semua baris terkait
        $totalBobot = TahapMekanisme::where('id_cpl', $tahap->id_cpl)
            ->where('id_mk', $tahap->id_mk)
            ->where('id_cpmk', $tahap->id_cpmk)
            ->sum('bobot');

        TahapMekanisme::where('id_cpl', $tahap->id_cpl)
            ->where('id_mk', $tahap->id_mk)
            ->where('id_cpmk', $tahap->id_cpmk)
            ->update(['bobot_mk' => $totalBobot]);

        return redirect()->route('tahap-mekanisme-penilaian.index')->with('success', 'Data mekanisme penilaian berhasil ditambahkan!');
    }

    /**
     * Ambil satu record (untuk modal edit via AJAX).
     */
    public function show(TahapMekanisme $tahapMekanisme)
    {
        return response()->json($tahapMekanisme);
    }

    /**
     * Update data.
     */
    public function update(Request $request, TahapMekanisme $tahapMekanisme)
    {
        $request->validate([
            'tahap_penilaian' => 'required|string|max:255',
            'teknik_penilaian' => 'required|string|max:255',
            'instrumen' => 'required|string|max:255',
            'kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric',
        ]);

        // CPL/MK/CPMK dianggap tetap (tidak diedit dari halaman ini)
        $tahapMekanisme->update($request->only([
            'tahap_penilaian',
            'teknik_penilaian',
            'instrumen',
            'kriteria',
            'bobot',
        ]));

        // Recalculate total bobot untuk kombinasi CPL+MK+CPMK yang sama
        $totalBobot = TahapMekanisme::where('id_cpl', $tahapMekanisme->id_cpl)
            ->where('id_mk', $tahapMekanisme->id_mk)
            ->where('id_cpmk', $tahapMekanisme->id_cpmk)
            ->sum('bobot');

        TahapMekanisme::where('id_cpl', $tahapMekanisme->id_cpl)
            ->where('id_mk', $tahapMekanisme->id_mk)
            ->where('id_cpmk', $tahapMekanisme->id_cpmk)
            ->update(['bobot_mk' => $totalBobot]);

        return redirect()->route('tahap-mekanisme-penilaian.index')
            ->with('success', 'Data mekanisme penilaian berhasil diupdate!');
    }

    /**
     * Hapus satu data (route: DELETE /tahap-mekanisme-penilaian/{tahapMekanisme}).
     * Hanya baris yang dipilih saja yang dihapus, data lain tetap aman.
     */
    public function destroy(TahapMekanisme $tahapMekanisme)
    {
        try {
            // Simpan kombinasi CPL+MK+CPMK untuk recalculasi bobot_mk setelah delete
            $idCpl = $tahapMekanisme->id_cpl;
            $idMk = $tahapMekanisme->id_mk;
            $idCpmk = $tahapMekanisme->id_cpmk;

            // Hapus hanya satu baris ini
            $tahapMekanisme->delete();

            // Recalculate total bobot untuk kombinasi CPL+MK+CPMK yang sama
            $totalBobot = TahapMekanisme::where('id_cpl', $idCpl)
                ->where('id_mk', $idMk)
                ->where('id_cpmk', $idCpmk)
                ->sum('bobot');

            // Update bobot_mk di semua baris yang masih tersisa untuk kombinasi ini
            TahapMekanisme::where('id_cpl', $idCpl)
                ->where('id_mk', $idMk)
                ->where('id_cpmk', $idCpmk)
                ->update(['bobot_mk' => $totalBobot]);

            return redirect()->route('tahap-mekanisme-penilaian.index')
                ->with('success', 'Satu data mekanisme penilaian berhasil dihapus!');
        } catch (QueryException $e) {
            return redirect()->route('tahap-mekanisme-penilaian.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus banyak data sekaligus (route: POST /tahap-mekanisme-penilaian/delete-multiple).
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tbl_tahap_mekanisme_penilaian,id',
        ]);

        try {
            // Ambil dulu kombinasi CPL+MK+CPMK dari baris yang akan dihapus
            $rows = TahapMekanisme::whereIn('id', $request->ids)
                ->get(['id_cpl', 'id_mk', 'id_cpmk']);

            // Hapus baris-baris yang dipilih berdasarkan primary key "id"
            $deletedCount = TahapMekanisme::whereIn('id', $request->ids)->delete();

            // Untuk setiap kombinasi unik CPL+MK+CPMK, hitung ulang bobot_mk
            $uniqueCombos = $rows
                ->map(function ($row) {
                    return [
                        'id_cpl' => $row->id_cpl,
                        'id_mk' => $row->id_mk,
                        'id_cpmk' => $row->id_cpmk,
                    ];
                })
                ->unique();

            foreach ($uniqueCombos as $combo) {
                $totalBobot = TahapMekanisme::where('id_cpl', $combo['id_cpl'])
                    ->where('id_mk', $combo['id_mk'])
                    ->where('id_cpmk', $combo['id_cpmk'])
                    ->sum('bobot');

                TahapMekanisme::where('id_cpl', $combo['id_cpl'])
                    ->where('id_mk', $combo['id_mk'])
                    ->where('id_cpmk', $combo['id_cpmk'])
                    ->update(['bobot_mk' => $totalBobot]);
            }

            return redirect()->route('tahap-mekanisme-penilaian.index')
                ->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            return redirect()->route('tahap-mekanisme-penilaian.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
