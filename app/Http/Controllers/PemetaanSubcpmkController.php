<?php

namespace App\Http\Controllers;

use App\Models\PemetaanSubcpmk;
use App\Models\PemetaanCplCpmkMk;
use App\Models\MataKuliah;
use App\Models\CapaianLulusanProdi;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class PemetaanSubcpmkController extends Controller
{
    /**
     * Display a listing of the Pemetaan Sub-CPMK.
     */
    public function index()
    {
        $data = PemetaanSubcpmk::orderBy('id_mk', 'asc')
            ->orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->orderBy('sub_cpmk', 'asc')
            ->get();

        $mks = MataKuliah::orderBy('id', 'asc')->get();
        $cpls = CapaianLulusanProdi::orderBy('id', 'asc')->get();
        $cpmks = PemetaanCplCpmkMk::orderBy('id_mk', 'asc')
            ->orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        return view('pemetaan-subcpmk', compact('data', 'mks', 'cpls', 'cpmks'));
    }

    /**
     * Store a newly created Pemetaan Sub-CPMK in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_mk' => 'required|string|max:10',
            'id_cpl' => 'required|string|max:10',
            'id_cpmk' => 'required|string|max:10',
            'desk_cpmk' => 'required|string',
            'sub_cpmk' => 'required|string|max:20',
            'desk_subcpmk' => 'required|string|unique:tbl_pemetaan_mk_cpmk_subcpmk,desk_subcpmk',
        ]);

        PemetaanSubcpmk::create($request->only([
            'id_mk',
            'id_cpl',
            'id_cpmk',
            'desk_cpmk',
            'sub_cpmk',
            'desk_subcpmk',
        ]));

        return redirect()->route('pemetaan-subcpmk.index')->with('success', 'Data Sub-CPMK berhasil ditambahkan!');
    }

    /**
     * Display the specified Pemetaan Sub-CPMK.
     */
    public function show(PemetaanSubcpmk $pemetaanSubcpmk)
    {
        return response()->json($pemetaanSubcpmk);
    }

    /**
     * Update the specified Pemetaan Sub-CPMK in storage.
     */
    public function update(Request $request, PemetaanSubcpmk $pemetaanSubcpmk)
    {
        $oldDesk = $pemetaanSubcpmk->desk_subcpmk;
        $newDesk = $request->desk_subcpmk;

        $request->validate([
            // MK/CPL/CPMK diset readonly di UI, tapi tetap divalidasi agar data lengkap saat create ulang
            'id_mk' => 'required|string|max:10',
            'id_cpl' => 'required|string|max:10',
            'id_cpmk' => 'required|string|max:10',
            'desk_cpmk' => 'required|string',
            'sub_cpmk' => 'required|string|max:20',
            'desk_subcpmk' => 'required|string|unique:tbl_pemetaan_mk_cpmk_subcpmk,desk_subcpmk,' . $oldDesk . ',desk_subcpmk',
        ]);

        if ($oldDesk !== $newDesk) {
            $data = $request->only(['id_mk', 'id_cpl', 'id_cpmk', 'desk_cpmk', 'sub_cpmk', 'desk_subcpmk']);
            $pemetaanSubcpmk->delete();
            PemetaanSubcpmk::create($data);
        } else {
            $pemetaanSubcpmk->update($request->only(['sub_cpmk', 'desk_subcpmk']));
        }

        return redirect()->route('pemetaan-subcpmk.index')->with('success', 'Data Sub-CPMK berhasil diupdate!');
    }

    /**
     * Remove the specified Pemetaan Sub-CPMK from storage.
     */
    public function destroy(PemetaanSubcpmk $pemetaanSubcpmk)
    {
        try {
            $pemetaanSubcpmk->delete();
            return redirect()->route('pemetaan-subcpmk.index')->with('success', 'Data Sub-CPMK berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('pemetaan-subcpmk.index')
                    ->withErrors(['error' => 'Data tidak dapat dihapus karena masih digunakan di tabel lain.']);
            }
            return redirect()->route('pemetaan-subcpmk.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete multiple Pemetaan Sub-CPMK.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tbl_pemetaan_mk_cpmk_subcpmk,desk_subcpmk',
        ]);

        try {
            $deletedCount = PemetaanSubcpmk::whereIn('desk_subcpmk', $request->ids)->delete();
            return redirect()->route('pemetaan-subcpmk.index')->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('pemetaan-subcpmk.index')
                    ->withErrors(['error' => 'Beberapa data tidak dapat dihapus karena masih digunakan di tabel lain.']);
            }
            return redirect()->route('pemetaan-subcpmk.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
