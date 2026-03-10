<?php

namespace App\Http\Controllers;

use App\Models\CapaianLulusanProdi;
use App\Models\CapaianPembelajaranMataKuliah;
use App\Models\PemetaanCplCpmkMk;
use App\Models\PemetaanSubcpmk;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapaianPembelajaranMataKuliahController extends Controller
{
    public function index()
    {
        $pemetaan = CapaianPembelajaranMataKuliah::orderBy('id_cpl', 'asc')

            ->orderBy('id_cpmk', 'asc')
            ->get();

        $cpls = CapaianLulusanProdi::orderBy('id', 'asc')->get();

        return view('capaian-pembelajaran-mata-kuliah', compact('pemetaan', 'cpls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cpl' => 'required|string|max:10',
            'desk_cpl' => 'required|string',
            'id_cpmk' => 'required|string|max:20',
            'desk_cpmk' => 'required|string|unique:mst_cpmk,desk_cpmk',
        ]);

        CapaianPembelajaranMataKuliah::create($request->only(['id_cpl', 'desk_cpl', 'id_cpmk', 'desk_cpmk']));

        return redirect()->route('capaian-pembelajaran-mata-kuliah.index')->with('success', 'Data capaian pembelajaran berhasil ditambahkan!');
    }

    public function show(CapaianPembelajaranMataKuliah $capaianPembelajaranMataKuliah)
    {
        return response()->json($capaianPembelajaranMataKuliah);
    }

    public function update(Request $request, CapaianPembelajaranMataKuliah $capaianPembelajaranMataKuliah)
    {
        $oldDeskCpmk = $capaianPembelajaranMataKuliah->desk_cpmk;
        $newDeskCpmk = $request->desk_cpmk;

        $request->validate([
            'id_cpl' => 'required|string|max:10',
            'desk_cpl' => 'required|string',
            'id_cpmk' => 'required|string|max:20',
            'desk_cpmk' => 'required|string|unique:mst_cpmk,desk_cpmk,' . $oldDeskCpmk . ',desk_cpmk',
        ]);

        DB::transaction(function () use ($request, $capaianPembelajaranMataKuliah, $oldDeskCpmk, $newDeskCpmk) {
            $masterPayload = $request->only(['id_cpl', 'desk_cpl', 'id_cpmk', 'desk_cpmk']);

            if ($oldDeskCpmk !== $newDeskCpmk) {
                $capaianPembelajaranMataKuliah->delete();
                CapaianPembelajaranMataKuliah::create($masterPayload);
            } else {
                $capaianPembelajaranMataKuliah->update($masterPayload);
            }

            // Sinkronkan data turunan agar pemetaan CPL-CPMK-MK tetap terhubung ke master CPMK.
            PemetaanCplCpmkMk::where('desk_cpmk', $oldDeskCpmk)->update([
                'id_cpl' => $request->id_cpl,
                'desk_cpl' => $request->desk_cpl,
                'id_cpmk' => $request->id_cpmk,
                'desk_cpmk' => $request->desk_cpmk,
            ]);

            // Sinkronkan juga pemetaan Sub-CPMK yang memakai data CPMK dari tabel pemetaan.
            PemetaanSubcpmk::where('desk_cpmk', $oldDeskCpmk)->update([
                'id_cpl' => $request->id_cpl,
                'id_cpmk' => $request->id_cpmk,
                'desk_cpmk' => $request->desk_cpmk,
            ]);
        });

        return redirect()->route('capaian-pembelajaran-mata-kuliah.index')->with('success', 'Data capaian pembelajaran berhasil diupdate!');
    }

    public function destroy(CapaianPembelajaranMataKuliah $capaianPembelajaranMataKuliah)
    {
        $deskCpmk = $capaianPembelajaranMataKuliah->desk_cpmk;

        $isUsedInPemetaan = PemetaanCplCpmkMk::where('desk_cpmk', $deskCpmk)->exists();
        $isUsedInSubCpmk = PemetaanSubcpmk::where('desk_cpmk', $deskCpmk)->exists();

        if ($isUsedInPemetaan || $isUsedInSubCpmk) {
            return redirect()->route('capaian-pembelajaran-mata-kuliah.index')
                ->withErrors(['error' => 'CPMK tidak dapat dihapus karena masih dipakai di Pemetaan CPL-CPMK-MK atau Sub-CPMK.']);
        }

        try {
            $capaianPembelajaranMataKuliah->delete();
            return redirect()->route('capaian-pembelajaran-mata-kuliah.index')->with('success', 'Data capaian pembelajaran berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('capaian-pembelajaran-mata-kuliah.index')
                    ->withErrors(['error' => 'Data tidak dapat dihapus karena masih digunakan di tabel lain.']);
            }
            return redirect()->route('capaian-pembelajaran-mata-kuliah.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mst_cpmk,desk_cpmk',
        ]);

        $usedInPemetaan = PemetaanCplCpmkMk::whereIn('desk_cpmk', $request->ids)->pluck('desk_cpmk');
        $usedInSubCpmk = PemetaanSubcpmk::whereIn('desk_cpmk', $request->ids)->pluck('desk_cpmk');
        $blockedIds = $usedInPemetaan->merge($usedInSubCpmk)->unique()->values();

        if ($blockedIds->isNotEmpty()) {
            return redirect()->route('capaian-pembelajaran-mata-kuliah.index')
                ->withErrors(['error' => 'Beberapa CPMK tidak dapat dihapus karena masih dipakai di pemetaan: ' . $blockedIds->implode(', ')]);
        }

        try {
            $deletedCount = CapaianPembelajaranMataKuliah::whereIn('desk_cpmk', $request->ids)->delete();
            return redirect()->route('capaian-pembelajaran-mata-kuliah.index')->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('capaian-pembelajaran-mata-kuliah.index')
                    ->withErrors(['error' => 'Beberapa data tidak dapat dihapus karena masih digunakan di tabel lain.']);
            }
            return redirect()->route('capaian-pembelajaran-mata-kuliah.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
