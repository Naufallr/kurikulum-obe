<?php

namespace App\Http\Controllers;

use App\Models\BahanKajian;
use App\Models\CapaianLulusanProdi;
use App\Models\PemetaanCplBk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemetaanCplBkController extends Controller
{
    public function index()
    {
        $cpls = CapaianLulusanProdi::orderBy('id', 'asc')->get();
        $bks = BahanKajian::orderBy('id', 'asc')->get();
        $mappings = PemetaanCplBk::all();

        $mapped = [];
        foreach ($mappings as $mapping) {
            $mapped[$mapping->id_bk][$mapping->id_cpl] = true;
        }

        return view('pemetaan-cpl-bk', compact('cpls', 'bks', 'mapped'));
    }

    public function store(Request $request)
    {
        $mapping = $request->input('mapping', []);
        $payload = [];

        foreach ($mapping as $bkId => $cplItems) {
            foreach ($cplItems as $cplId => $value) {
                if ($value) {
                    $payload[] = [
                        'id_bk' => $bkId,
                        'id_cpl' => $cplId,
                    ];
                }
            }
        }

        DB::transaction(function () use ($payload) {
            DB::table('tbl_cpl_bk')->delete();
            if (!empty($payload)) {
                DB::table('tbl_cpl_bk')->insert($payload);
            }
        });

        return redirect()
            ->route('pemetaan-cpl-bk.index')
            ->with('success', 'Pemetaan CPL-BK berhasil disimpan!');
    }
}
