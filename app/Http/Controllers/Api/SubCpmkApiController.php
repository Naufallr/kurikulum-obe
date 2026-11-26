<?php

namespace App\Http\Controllers\Api;

use App\Models\SubCpmk;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubCpmkApiController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $records = SubCpmk::orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->orderBy('id_subcpmk', 'asc')
            ->get();

        return $this->success($records, 'Sub-CPMK berhasil diambil.');
    }

    public function show(string $record): JsonResponse
    {
        $subCpmk = SubCpmk::where('id_subcpmk', urldecode($record))->first();

        if (!$subCpmk) {
            return $this->notFound('Sub-CPMK');
        }

        return $this->success($subCpmk, 'Sub-CPMK berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_cpl' => 'required|string|max:10|exists:mst_cplp,id',
            'id_cpmk' => 'required|string|max:20|exists:mst_cpmk,id_cpmk',
            'desk_cpmk' => 'required|string|exists:mst_cpmk,desk_cpmk',
            'id_subcpmk' => 'required|string|max:30|unique:mst_subcpmk,id_subcpmk',
            'desk_subcpmk' => 'required|string|unique:mst_subcpmk,desk_subcpmk',
        ]);

        $record = SubCpmk::create($validated);

        return $this->success($record, 'Sub-CPMK berhasil dibuat.', 201);
    }

    public function update(Request $request, string $record): JsonResponse
    {
        $subCpmk = SubCpmk::where('id_subcpmk', urldecode($record))->first();

        if (!$subCpmk) {
            return $this->notFound('Sub-CPMK');
        }

        $validated = $request->validate([
            'id_cpl' => 'required|string|max:10|exists:mst_cplp,id',
            'id_cpmk' => 'required|string|max:20|exists:mst_cpmk,id_cpmk',
            'desk_cpmk' => 'required|string|exists:mst_cpmk,desk_cpmk',
            'id_subcpmk' => [
                'required',
                'string',
                'max:30',
                Rule::unique('mst_subcpmk', 'id_subcpmk')->ignore($subCpmk->id_subcpmk, 'id_subcpmk'),
            ],
            'desk_subcpmk' => [
                'required',
                'string',
                Rule::unique('mst_subcpmk', 'desk_subcpmk')->ignore($subCpmk->desk_subcpmk, 'desk_subcpmk'),
            ],
        ]);

        if ($subCpmk->id_subcpmk !== $validated['id_subcpmk']) {
            $subCpmk->delete();
            SubCpmk::create($validated);
        } else {
            $subCpmk->update($validated);
        }

        $freshRecord = SubCpmk::where('id_subcpmk', $validated['id_subcpmk'])->first();

        return $this->success($freshRecord, 'Sub-CPMK berhasil diperbarui.');
    }

    public function destroy(string $record): JsonResponse
    {
        $subCpmk = SubCpmk::where('id_subcpmk', urldecode($record))->first();

        if (!$subCpmk) {
            return $this->notFound('Sub-CPMK');
        }

        try {
            $subCpmk->delete();

            return $this->deleted('Sub-CPMK berhasil dihapus.');
        } catch (QueryException $exception) {
            return $this->handleQueryException($exception, 'Gagal menghapus Sub-CPMK.');
        }
    }
}
