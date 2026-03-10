<?php

namespace App\Http\Controllers;

use App\Models\PemetaanCplCpmkMk;
use App\Models\CapaianPembelajaranMataKuliah;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class PemetaanCplCpmkMkController extends Controller
{
    /**
     * Display a listing of the Pemetaan CPL CPMK MK.
     */
    public function index()
    {
        $pemetaan = PemetaanCplCpmkMk::orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();
        $cpmkMaster = CapaianPembelajaranMataKuliah::orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();
        $mks = MataKuliah::orderBy('id', 'asc')->get();
        
        return view('pemetaan-cpl-cpmk-mk', compact('pemetaan', 'cpmkMaster', 'mks'));
    }

    /**
     * Store a newly created Pemetaan CPL CPMK MK in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'source_desk_cpmk' => 'required|string|exists:mst_cpmk,desk_cpmk|unique:tbl_pemetaan_cpl_cpmk_mk,desk_cpmk',
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
        ]);

        $source = CapaianPembelajaranMataKuliah::findOrFail($request->source_desk_cpmk);

        PemetaanCplCpmkMk::create([
            'id_cpl' => $source->id_cpl,
            'desk_cpl' => $source->desk_cpl,
            'id_cpmk' => $source->id_cpmk,
            'desk_cpmk' => $source->desk_cpmk,
            'id_mk' => $request->id_mk,
        ]);

        return redirect()->route('pemetaan-cpl-cpmk-mk.index')->with('success', 'Data pemetaan berhasil ditambahkan!');
    }

    /**
     * Display the specified Pemetaan CPL CPMK MK.
     */
    public function show(PemetaanCplCpmkMk $pemetaanCplCpmkMk)
    {
        return response()->json($pemetaanCplCpmkMk);
    }

    /**
     * Update the specified Pemetaan CPL CPMK MK in storage.
     */
    public function update(Request $request, PemetaanCplCpmkMk $pemetaanCplCpmkMk)
    {
        $oldDeskCpmk = $pemetaanCplCpmkMk->desk_cpmk;
        $newDeskCpmk = $request->source_desk_cpmk;

        $request->validate([
            'source_desk_cpmk' => [
                'required',
                'string',
                'exists:mst_cpmk,desk_cpmk',
                Rule::unique('tbl_pemetaan_cpl_cpmk_mk', 'desk_cpmk')->ignore($oldDeskCpmk, 'desk_cpmk'),
            ],
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
        ]);

        $source = CapaianPembelajaranMataKuliah::findOrFail($newDeskCpmk);

        // Jika desk_cpmk berubah, kita perlu menghapus yang lama dan membuat yang baru
        // Tapi tetap gunakan CPL yang sudah ada (tidak diubah)
        if ($oldDeskCpmk !== $newDeskCpmk) {
            $data = [
                'id_cpl' => $source->id_cpl,
                'desk_cpl' => $source->desk_cpl,
                'id_cpmk' => $source->id_cpmk,
                'desk_cpmk' => $source->desk_cpmk,
                'id_mk' => $request->id_mk,
            ];
            $pemetaanCplCpmkMk->delete();
            PemetaanCplCpmkMk::create($data);
        } else {
            // Jika CPMK tetap sama, cukup update MK.
            $pemetaanCplCpmkMk->update($request->only(['id_mk']));
        }

        return redirect()->route('pemetaan-cpl-cpmk-mk.index')->with('success', 'Data pemetaan berhasil diupdate!');
    }

    /**
     * Remove the specified Pemetaan CPL CPMK MK from storage.
     */
    public function destroy(PemetaanCplCpmkMk $pemetaanCplCpmkMk)
    {
        try {
            $pemetaanCplCpmkMk->delete();
            return redirect()->route('pemetaan-cpl-cpmk-mk.index')->with('success', 'Data pemetaan berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('pemetaan-cpl-cpmk-mk.index')
                    ->withErrors(['error' => 'Data tidak dapat dihapus karena masih digunakan di tabel lain.']);
            }
            return redirect()->route('pemetaan-cpl-cpmk-mk.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete multiple Pemetaan CPL CPMK MK.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tbl_pemetaan_cpl_cpmk_mk,desk_cpmk',
        ]);

        try {
            $deletedCount = PemetaanCplCpmkMk::whereIn('desk_cpmk', $request->ids)->delete();
            return redirect()->route('pemetaan-cpl-cpmk-mk.index')->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('pemetaan-cpl-cpmk-mk.index')
                    ->withErrors(['error' => 'Beberapa data tidak dapat dihapus karena masih digunakan di tabel lain.']);
            }
            return redirect()->route('pemetaan-cpl-cpmk-mk.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
