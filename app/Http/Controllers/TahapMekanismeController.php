<?php

namespace App\Http\Controllers;

use App\Models\TahapMekanisme;
use App\Models\PemetaanCplCpmkMk;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahapMekanismeController extends Controller
{
    /**
     * Sinkronkan bobot_mk untuk semua baris dengan kombinasi CPL+MK yang sama.
     */
    private function syncBobotMkByCplMk(string $idCpl, string $idMk): void
    {
        $totalBobot = TahapMekanisme::where('id_cpl', $idCpl)
            ->where('id_mk', $idMk)
            ->sum('bobot');

        TahapMekanisme::where('id_cpl', $idCpl)
            ->where('id_mk', $idMk)
            ->update(['bobot_mk' => $totalBobot]);
    }

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
            'id_cpmk' => [
                'required',
                'string',
                'max:20',
                Rule::exists('tbl_pemetaan_cpl_cpmk_mk', 'id_cpmk')->where(function ($query) use ($request) {
                    $query->where('id_cpl', $request->id_cpl)
                        ->where('id_mk', $request->id_mk);
                }),
            ],
            'tahap_penilaian' => 'required|string|max:255',
            'teknik_penilaian' => 'required|string|max:255',
            'instrumen' => 'required|string|max:255',
            'kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0',
        ]);

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

        // Bobot MK = SUM bobot untuk kombinasi CPL+MK yang sama
        $this->syncBobotMkByCplMk($tahap->id_cpl, $tahap->id_mk);

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
            'bobot' => 'required|numeric|min:0',
        ]);

        // CPL/MK/CPMK dianggap tetap (tidak diedit dari halaman ini)
        $tahapMekanisme->update($request->only([
            'tahap_penilaian',
            'teknik_penilaian',
            'instrumen',
            'kriteria',
            'bobot',
        ]));

        // Bobot MK = SUM bobot untuk kombinasi CPL+MK yang sama
        $this->syncBobotMkByCplMk($tahapMekanisme->id_cpl, $tahapMekanisme->id_mk);

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
            // Simpan kombinasi CPL+MK untuk recalculasi bobot_mk setelah delete
            $idCpl = $tahapMekanisme->id_cpl;
            $idMk = $tahapMekanisme->id_mk;

            // Hapus hanya satu baris ini
            $tahapMekanisme->delete();

            // Bobot MK = SUM bobot untuk kombinasi CPL+MK yang sama
            $this->syncBobotMkByCplMk($idCpl, $idMk);

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
            // Ambil dulu kombinasi CPL+MK dari baris yang akan dihapus
            $rows = TahapMekanisme::whereIn('id', $request->ids)
                ->get(['id_cpl', 'id_mk']);

            // Hapus baris-baris yang dipilih berdasarkan primary key "id"
            $deletedCount = TahapMekanisme::whereIn('id', $request->ids)->delete();

            // Untuk setiap kombinasi unik CPL+MK, hitung ulang bobot_mk
            $uniqueCombos = $rows
                ->map(function ($row) {
                    return [
                        'id_cpl' => $row->id_cpl,
                        'id_mk' => $row->id_mk,
                    ];
                })
                ->unique();

            foreach ($uniqueCombos as $combo) {
                $this->syncBobotMkByCplMk($combo['id_cpl'], $combo['id_mk']);
            }

            return redirect()->route('tahap-mekanisme-penilaian.index')
                ->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            return redirect()->route('tahap-mekanisme-penilaian.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
