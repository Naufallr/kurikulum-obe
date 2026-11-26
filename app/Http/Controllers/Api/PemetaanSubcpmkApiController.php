<?php

namespace App\Http\Controllers\Api;

use App\Models\PemetaanCplCpmkMk;
use App\Models\PemetaanSubcpmk;
use App\Models\SubCpmk;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PemetaanSubcpmkApiController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $records = PemetaanSubcpmk::orderBy('id_mk', 'asc')
            ->orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->orderBy('sub_cpmk', 'asc')
            ->get();

        return $this->success($records, 'Pemetaan MK-CPMK-SubCPMK berhasil diambil.');
    }

    public function show(string $record): JsonResponse
    {
        $mapping = PemetaanSubcpmk::where('sub_cpmk', urldecode($record))->first();

        if (!$mapping) {
            return $this->notFound('Pemetaan MK-CPMK-SubCPMK');
        }

        return $this->success($mapping, 'Pemetaan MK-CPMK-SubCPMK berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
            'source_id_subcpmk' => 'required|string|exists:mst_subcpmk,id_subcpmk',
        ]);

        $source = SubCpmk::findOrFail($validated['source_id_subcpmk']);
        $mappingCpmk = $this->findMatchingCpmkMapping($validated['id_mk'], $source->id_cpl, $source->id_cpmk);

        if (!$mappingCpmk) {
            return $this->error("Sub-CPMK {$source->id_subcpmk} tidak cocok dengan pemetaan CPL-CPMK pada MK {$validated['id_mk']}.", 422);
        }

        $exists = PemetaanSubcpmk::where('sub_cpmk', $source->id_subcpmk)->exists();
        if ($exists) {
            return $this->error("Sub-CPMK {$source->id_subcpmk} sudah ada di pemetaan.", 409);
        }

        $record = PemetaanSubcpmk::create([
            'id_mk' => $validated['id_mk'],
            'id_cpl' => $source->id_cpl,
            'id_cpmk' => $source->id_cpmk,
            'desk_cpmk' => $mappingCpmk->desk_cpmk,
            'sub_cpmk' => $source->id_subcpmk,
            'desk_subcpmk' => $source->desk_subcpmk,
        ]);

        return $this->success($record, 'Pemetaan MK-CPMK-SubCPMK berhasil dibuat.', 201);
    }

    public function update(Request $request, string $record): JsonResponse
    {
        $mapping = PemetaanSubcpmk::where('sub_cpmk', urldecode($record))->first();

        if (!$mapping) {
            return $this->notFound('Pemetaan MK-CPMK-SubCPMK');
        }

        $validated = $request->validate([
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
            'source_id_subcpmk' => 'required|string|exists:mst_subcpmk,id_subcpmk',
        ]);

        $source = SubCpmk::findOrFail($validated['source_id_subcpmk']);
        $mappingCpmk = $this->findMatchingCpmkMapping($validated['id_mk'], $source->id_cpl, $source->id_cpmk);

        if (!$mappingCpmk) {
            return $this->error("Sub-CPMK {$source->id_subcpmk} tidak cocok dengan pemetaan CPL-CPMK pada MK {$validated['id_mk']}.", 422);
        }

        if ($mapping->sub_cpmk !== $source->id_subcpmk) {
            $isUsed = PemetaanSubcpmk::where('sub_cpmk', $source->id_subcpmk)->exists();
            if ($isUsed) {
                return $this->error("Sub-CPMK {$source->id_subcpmk} sudah dipakai pada pemetaan lain.", 409);
            }

            $mapping->delete();

            $mapping = PemetaanSubcpmk::create([
                'id_mk' => $validated['id_mk'],
                'id_cpl' => $source->id_cpl,
                'id_cpmk' => $source->id_cpmk,
                'desk_cpmk' => $mappingCpmk->desk_cpmk,
                'sub_cpmk' => $source->id_subcpmk,
                'desk_subcpmk' => $source->desk_subcpmk,
            ]);
        } else {
            $mapping->update([
                'id_mk' => $validated['id_mk'],
                'id_cpl' => $source->id_cpl,
                'id_cpmk' => $source->id_cpmk,
                'desk_cpmk' => $mappingCpmk->desk_cpmk,
                'desk_subcpmk' => $source->desk_subcpmk,
            ]);
        }

        return $this->success($mapping->fresh(), 'Pemetaan MK-CPMK-SubCPMK berhasil diperbarui.');
    }

    public function destroy(string $record): JsonResponse
    {
        $mapping = PemetaanSubcpmk::where('sub_cpmk', urldecode($record))->first();

        if (!$mapping) {
            return $this->notFound('Pemetaan MK-CPMK-SubCPMK');
        }

        try {
            $mapping->delete();

            return $this->deleted('Pemetaan MK-CPMK-SubCPMK berhasil dihapus.');
        } catch (QueryException $exception) {
            return $this->handleQueryException($exception, 'Gagal menghapus pemetaan MK-CPMK-SubCPMK.');
        }
    }

    private function findMatchingCpmkMapping(string $idMk, string $idCpl, string $idCpmk): ?PemetaanCplCpmkMk
    {
        return PemetaanCplCpmkMk::where('id_mk', $idMk)
            ->where('id_cpl', $idCpl)
            ->where('id_cpmk', $idCpmk)
            ->first();
    }
}
