<?php

namespace App\Http\Controllers;

use App\Models\CapaianLulusanProdi;
use App\Models\CapaianPembelajaranMataKuliah;
use App\Models\SubCpmk;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SubCpmkController extends Controller
{
    /**
     * Display a listing of the Sub-CPMK.
     */
    public function index()
    {
        $data = SubCpmk::orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->orderBy('id_subcpmk', 'asc')
            ->get();

        $cpls = CapaianLulusanProdi::orderBy('id', 'asc')->get();
        $cpmks = CapaianPembelajaranMataKuliah::orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        return view('sub-cpmk', compact('data', 'cpls', 'cpmks'));
    }

    /**
     * Store a newly created Sub-CPMK.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_cpl' => 'required|string|max:10|exists:mst_cplp,id',
            'id_cpmk' => 'required|string|max:20|exists:mst_cpmk,id_cpmk',
            'desk_cpmk' => 'required|string|exists:mst_cpmk,desk_cpmk',
            'id_subcpmk' => 'required|string|max:30|unique:mst_subcpmk,id_subcpmk',
            'desk_subcpmk' => 'required|string|unique:mst_subcpmk,desk_subcpmk',
        ]);

        SubCpmk::create($request->only([
            'id_cpl',
            'id_cpmk',
            'desk_cpmk',
            'id_subcpmk',
            'desk_subcpmk',
        ]));

        return redirect()->route('sub-cpmk.index')->with('success', 'Data Sub-CPMK berhasil ditambahkan!');
    }

    /**
     * Display the specified Sub-CPMK.
     */
    public function show(SubCpmk $subCpmk)
    {
        return response()->json($subCpmk);
    }

    /**
     * Update the specified Sub-CPMK.
     */
    public function update(Request $request, SubCpmk $subCpmk)
    {
        $oldIdSubcpmk = $subCpmk->id_subcpmk;
        $newIdSubcpmk = $request->id_subcpmk;

        $request->validate([
            'id_cpl' => 'required|string|max:10|exists:mst_cplp,id',
            'id_cpmk' => 'required|string|max:20|exists:mst_cpmk,id_cpmk',
            'desk_cpmk' => 'required|string|exists:mst_cpmk,desk_cpmk',
            'id_subcpmk' => 'required|string|max:30|unique:mst_subcpmk,id_subcpmk,' . $oldIdSubcpmk . ',id_subcpmk',
            'desk_subcpmk' => 'required|string|unique:mst_subcpmk,desk_subcpmk,' . $subCpmk->desk_subcpmk . ',desk_subcpmk',
        ]);

        if ($oldIdSubcpmk !== $newIdSubcpmk) {
            $payload = $request->only(['id_cpl', 'id_cpmk', 'desk_cpmk', 'id_subcpmk', 'desk_subcpmk']);
            $subCpmk->delete();
            SubCpmk::create($payload);
        } else {
            $subCpmk->update($request->only(['id_cpl', 'id_cpmk', 'desk_cpmk', 'desk_subcpmk']));
        }

        return redirect()->route('sub-cpmk.index')->with('success', 'Data Sub-CPMK berhasil diupdate!');
    }

    /**
     * Remove the specified Sub-CPMK.
     */
    public function destroy(SubCpmk $subCpmk)
    {
        try {
            $subCpmk->delete();
            return redirect()->route('sub-cpmk.index')->with('success', 'Data Sub-CPMK berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('sub-cpmk.index')
                    ->withErrors(['error' => 'Data tidak dapat dihapus karena masih digunakan di tabel lain.']);
            }

            return redirect()->route('sub-cpmk.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete multiple Sub-CPMK.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mst_subcpmk,id_subcpmk',
        ]);

        try {
            $deletedCount = SubCpmk::whereIn('id_subcpmk', $request->ids)->delete();
            return redirect()->route('sub-cpmk.index')->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('sub-cpmk.index')
                    ->withErrors(['error' => 'Beberapa data tidak dapat dihapus karena masih digunakan di tabel lain.']);
            }

            return redirect()->route('sub-cpmk.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
