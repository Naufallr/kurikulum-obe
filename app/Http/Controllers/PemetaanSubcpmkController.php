<?php

namespace App\Http\Controllers;

use App\Models\PemetaanSubcpmk;
use App\Models\PemetaanCplCpmkMk;
use App\Models\MataKuliah;
use App\Models\SubCpmk;
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
        $subcpmkMaster = SubCpmk::orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->orderBy('id_subcpmk', 'asc')
            ->get();

        return view('pemetaan-subcpmk', compact('data', 'mks', 'cpls', 'subcpmkMaster'));
    }

    /**
     * Store a newly created Pemetaan Sub-CPMK in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
            'source_id_subcpmk' => 'required|string|exists:mst_subcpmk,id_subcpmk',
        ]);

        $source = SubCpmk::findOrFail($request->source_id_subcpmk);
        $mapping = PemetaanCplCpmkMk::where('id_mk', $request->id_mk)
            ->where('id_cpl', $source->id_cpl)
            ->where('id_cpmk', $source->id_cpmk)
            ->first();

        if (!$mapping) {
            return redirect()->route('pemetaan-subcpmk.index')
                ->withErrors(['error' => "Sub-CPMK {$source->id_subcpmk} tidak cocok dengan pemetaan CPL-CPMK pada MK {$request->id_mk}."])
                ->withInput();
        }

        $exists = PemetaanSubcpmk::where('desk_subcpmk', $source->desk_subcpmk)->exists();
        if ($exists) {
            return redirect()->route('pemetaan-subcpmk.index')
                ->withErrors(['error' => "Sub-CPMK {$source->id_subcpmk} sudah ada di pemetaan."])
                ->withInput();
        }

        PemetaanSubcpmk::create([
            'id_mk' => $request->id_mk,
            'id_cpl' => $source->id_cpl,
            'id_cpmk' => $source->id_cpmk,
            'desk_cpmk' => $mapping->desk_cpmk,
            'sub_cpmk' => $source->id_subcpmk,
            'desk_subcpmk' => $source->desk_subcpmk,
        ]);

        return redirect()->route('pemetaan-subcpmk.index')->with('success', 'Data Sub-CPMK berhasil ditambahkan dari database.');
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
        $request->validate([
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
            'source_id_subcpmk' => 'required|string|exists:mst_subcpmk,id_subcpmk',
        ]);

        $source = SubCpmk::findOrFail($request->source_id_subcpmk);
        $mapping = PemetaanCplCpmkMk::where('id_mk', $request->id_mk)
            ->where('id_cpl', $source->id_cpl)
            ->where('id_cpmk', $source->id_cpmk)
            ->first();

        if (!$mapping) {
            return redirect()->route('pemetaan-subcpmk.index')
                ->withErrors(['error' => "Sub-CPMK {$source->id_subcpmk} tidak cocok dengan pemetaan CPL-CPMK pada MK {$request->id_mk}."])
                ->withInput();
        }

        $oldDesk = $pemetaanSubcpmk->desk_subcpmk;
        $newDesk = $source->desk_subcpmk;

        if ($oldDesk !== $newDesk) {
            $isDeskUsed = PemetaanSubcpmk::where('desk_subcpmk', $newDesk)->exists();
            if ($isDeskUsed) {
                return redirect()->route('pemetaan-subcpmk.index')
                    ->withErrors(['error' => "Sub-CPMK {$source->id_subcpmk} sudah dipakai pada pemetaan lain."])
                    ->withInput();
            }

            $data = [
                'id_mk' => $request->id_mk,
                'id_cpl' => $source->id_cpl,
                'id_cpmk' => $source->id_cpmk,
                'desk_cpmk' => $mapping->desk_cpmk,
                'sub_cpmk' => $source->id_subcpmk,
                'desk_subcpmk' => $source->desk_subcpmk,
            ];

            $pemetaanSubcpmk->delete();
            PemetaanSubcpmk::create($data);
        } else {
            $pemetaanSubcpmk->update([
                'id_mk' => $request->id_mk,
                'id_cpl' => $source->id_cpl,
                'id_cpmk' => $source->id_cpmk,
                'desk_cpmk' => $mapping->desk_cpmk,
                'sub_cpmk' => $source->id_subcpmk,
            ]);
        }

        return redirect()->route('pemetaan-subcpmk.index')->with('success', 'Data Sub-CPMK berhasil diupdate dari database.');
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
