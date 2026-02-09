<?php

namespace App\Http\Controllers;

use App\Models\BahanKajian;
use App\Models\MataKuliah;
use App\Models\PemetaanBkMk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemetaanBkMkController extends Controller
{
    public function index()
    {
        $bks = BahanKajian::orderBy('id', 'asc')->get();
        $mks = MataKuliah::orderBy('id', 'asc')->get();
        $mappings = PemetaanBkMk::all();

        $mapped = [];
        foreach ($mappings as $mapping) {
            $mapped[$mapping->id_mk][$mapping->id_bk] = true;
        }

        return view('pemetaan-bk-mk', compact('bks', 'mks', 'mapped'));
    }

    public function store(Request $request)
    {
        $mapping = $request->input('mapping', []);
        $payload = [];

        foreach ($mapping as $mkId => $bkItems) {
            foreach ($bkItems as $bkId => $value) {
                if ($value) {
                    $payload[] = [
                        'id_mk' => $mkId,
                        'id_bk' => $bkId,
                    ];
                }
            }
        }

        DB::transaction(function () use ($payload) {
            DB::table('tbl_bk_mk')->delete();
            if (!empty($payload)) {
                DB::table('tbl_bk_mk')->insert($payload);
            }
        });

        return redirect()
            ->route('pemetaan-bk-mk.index')
            ->with('success', 'Pemetaan BK-MK berhasil disimpan!');
    }
}
