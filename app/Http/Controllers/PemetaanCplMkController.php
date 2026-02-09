<?php

namespace App\Http\Controllers;

use App\Models\CapaianLulusanProdi;
use App\Models\MataKuliah;
use App\Models\PemetaanCplMk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemetaanCplMkController extends Controller
{
    public function index()
    {
        $cpls = CapaianLulusanProdi::orderBy('id', 'asc')->get();
        $mks = MataKuliah::orderBy('id', 'asc')->get();
        $mappings = PemetaanCplMk::all();

        $mapped = [];
        foreach ($mappings as $mapping) {
            $mapped[$mapping->id_mk][$mapping->id_cpl] = true;
        }

        return view('pemetaan-cpl-mk', compact('cpls', 'mks', 'mapped'));
    }

    public function store(Request $request)
    {
        $mapping = $request->input('mapping', []);
        $payload = [];

        foreach ($mapping as $mkId => $cplItems) {
            foreach ($cplItems as $cplId => $value) {
                if ($value) {
                    $payload[] = [
                        'id_mk' => $mkId,
                        'id_cpl' => $cplId,
                    ];
                }
            }
        }

        DB::transaction(function () use ($payload) {
            DB::table('tbl_cpl_mk')->delete();
            if (!empty($payload)) {
                DB::table('tbl_cpl_mk')->insert($payload);
            }
        });

        return redirect()
            ->route('pemetaan-cpl-mk.index')
            ->with('success', 'Pemetaan CPL-MK berhasil disimpan!');
    }
}
