<?php

namespace App\Http\Controllers;

use App\Models\BahanKajian;
use App\Models\CapaianLulusanProdi;
use App\Models\MataKuliah;
use App\Models\PemetaanCplBkMk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemetaanCplBkMkController extends Controller
{
    public function index()
    {
        $cpls = CapaianLulusanProdi::orderBy('id', 'asc')->get();
        $bks = BahanKajian::orderBy('id', 'asc')->get();
        $mks = MataKuliah::orderBy('id', 'asc')->get();
        $mappings = PemetaanCplBkMk::all();

        $selectedMk = [];
        foreach ($mappings as $mapping) {
            if (empty($selectedMk[$mapping->id_cpl][$mapping->id_bk])) {
                $selectedMk[$mapping->id_cpl][$mapping->id_bk] = $mapping->id_mk;
            }
        }

        return view('pemetaan-cpl-bk-mk', compact('cpls', 'bks', 'mks', 'selectedMk'));
    }

    public function store(Request $request)
    {
        $mapping = $request->input('mapping', []);
        $payload = [];

        foreach ($mapping as $cplId => $bkItems) {
            foreach ($bkItems as $bkId => $mkSelection) {
                // Backward compatible: support old checkbox matrix payload.
                if (is_array($mkSelection)) {
                    foreach ($mkSelection as $mkId => $value) {
                        if ($value) {
                            $payload[] = [
                                'id_cpl' => $cplId,
                                'id_bk' => $bkId,
                                'id_mk' => $mkId,
                            ];
                        }
                    }
                    continue;
                }

                if (!empty($mkSelection)) {
                    $payload[] = [
                        'id_cpl' => $cplId,
                        'id_bk' => $bkId,
                        'id_mk' => $mkSelection,
                    ];
                }
            }
        }

        DB::transaction(function () use ($payload) {
            DB::table('tbl_cpl_bk_mk')->delete();
            if (!empty($payload)) {
                DB::table('tbl_cpl_bk_mk')->insert($payload);
            }
        });

        return redirect()
            ->route('pemetaan-cpl-bk-mk.index')
            ->with('success', 'Pemetaan CPL-BK-MK berhasil disimpan!');
    }
}
