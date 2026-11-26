<?php

namespace App\Http\Controllers\Api;

use App\Models\CapaianPembelajaranMataKuliah;
use App\Models\PemetaanCplCpmkMk;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PemetaanCplCpmkMkApiController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $records = PemetaanCplCpmkMk::orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        return $this->success($records, 'Pemetaan CPL-CPMK-MK berhasil diambil.');
    }

    public function show(string $record): JsonResponse
    {
        $mapping = PemetaanCplCpmkMk::where('id_cpmk', urldecode($record))->first();

        if (!$mapping) {
            return $this->notFound('Pemetaan CPL-CPMK-MK');
        }

        return $this->success($mapping, 'Pemetaan CPL-CPMK-MK berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_cpmk' => 'required|string|exists:mst_cpmk,id_cpmk|unique:tbl_pemetaan_cpl_cpmk_mk,id_cpmk',
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
        ]);

        $source = CapaianPembelajaranMataKuliah::where('id_cpmk', $validated['id_cpmk'])->firstOrFail();

        $record = PemetaanCplCpmkMk::create([
            'id_cpl' => $source->id_cpl,
            'desk_cpl' => $source->desk_cpl,
            'id_cpmk' => $source->id_cpmk,
            'desk_cpmk' => $source->desk_cpmk,
            'id_mk' => $validated['id_mk'],
        ]);

        return $this->success($record, 'Pemetaan CPL-CPMK-MK berhasil dibuat.', 201);
    }

    public function update(Request $request, string $record): JsonResponse
    {
        $mapping = PemetaanCplCpmkMk::where('id_cpmk', urldecode($record))->first();

        if (!$mapping) {
            return $this->notFound('Pemetaan CPL-CPMK-MK');
        }

        $validated = $request->validate([
            'id_cpmk' => [
                'required',
                'string',
                'exists:mst_cpmk,id_cpmk',
                Rule::unique('tbl_pemetaan_cpl_cpmk_mk', 'id_cpmk')->ignore($mapping->id_cpmk, 'id_cpmk'),
            ],
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
        ]);

        $source = CapaianPembelajaranMataKuliah::where('id_cpmk', $validated['id_cpmk'])->firstOrFail();

        if ($mapping->id_cpmk !== $validated['id_cpmk']) {
            $mapping->delete();

            $mapping = PemetaanCplCpmkMk::create([
                'id_cpl' => $source->id_cpl,
                'desk_cpl' => $source->desk_cpl,
                'id_cpmk' => $source->id_cpmk,
                'desk_cpmk' => $source->desk_cpmk,
                'id_mk' => $validated['id_mk'],
            ]);
        } else {
            $mapping->update([
                'id_mk' => $validated['id_mk'],
            ]);
        }

        return $this->success($mapping->fresh(), 'Pemetaan CPL-CPMK-MK berhasil diperbarui.');
    }

    public function destroy(string $record): JsonResponse
    {
        $mapping = PemetaanCplCpmkMk::where('id_cpmk', urldecode($record))->first();

        if (!$mapping) {
            return $this->notFound('Pemetaan CPL-CPMK-MK');
        }

        try {
            $mapping->delete();

            return $this->deleted('Pemetaan CPL-CPMK-MK berhasil dihapus.');
        } catch (QueryException $exception) {
            return $this->handleQueryException($exception, 'Gagal menghapus pemetaan CPL-CPMK-MK.');
        }
    }
}
