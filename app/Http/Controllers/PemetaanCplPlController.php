<?php

namespace App\Http\Controllers;

use App\Models\CapaianLulusanProdi;
use App\Models\PemetaanCplPl;
use App\Models\ProfileLulusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemetaanCplPlController extends Controller
{
    /**
     * Display the mapping matrix.
     */
    public function index()
    {
        $cpls = CapaianLulusanProdi::orderBy('id', 'asc')->get();
        $pls = ProfileLulusan::orderBy('id', 'asc')->get();
        $mappings = PemetaanCplPl::all();

        $mapped = [];
        foreach ($mappings as $mapping) {
            $mapped[$mapping->id_cplp][$mapping->id_pl] = true;
        }

        return view('pemetaan-cpl-pl', compact('cpls', 'pls', 'mapped'));
    }

    /**
     * Store mapping matrix.
     */
    public function store(Request $request)
    {
        $mapping = $request->input('mapping', []);
        $payload = [];

        foreach ($mapping as $cplId => $plItems) {
            foreach ($plItems as $plId => $value) {
                if ($value) {
                    $payload[] = [
                        'id_cplp' => $cplId,
                        'id_pl' => $plId,
                    ];
                }
            }
        }

        DB::transaction(function () use ($payload) {
            DB::table('tbl_pl_cpl')->delete();
            if (!empty($payload)) {
                DB::table('tbl_pl_cpl')->insert($payload);
            }
        });

        return redirect()
            ->route('pemetaan-cpl-pl.index')
            ->with('success', 'Pemetaan CPL-PL berhasil disimpan!');
    }
}
