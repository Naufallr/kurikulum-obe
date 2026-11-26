<?php

namespace App\Http\Controllers\Api;

use App\Models\CapaianPembelajaranMataKuliah;
use App\Models\PemetaanCplCpmkMk;
use App\Models\PemetaanSubcpmk;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CapaianPembelajaranMataKuliahApiController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $records = CapaianPembelajaranMataKuliah::orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        return $this->success($records, 'Capaian Pembelajaran Mata Kuliah berhasil diambil.');
    }

    public function show(string $record): JsonResponse
    {
        $cpmk = CapaianPembelajaranMataKuliah::where('id_cpmk', urldecode($record))->first();

        if (!$cpmk) {
            return $this->notFound('CPMK');
        }

        return $this->success($cpmk, 'CPMK berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_cpl' => 'required|string|max:10',
            'desk_cpl' => 'required|string',
            'id_cpmk' => 'required|string|max:20|unique:mst_cpmk,id_cpmk',
            'desk_cpmk' => 'required|string|unique:mst_cpmk,desk_cpmk',
        ]);

        $record = CapaianPembelajaranMataKuliah::create($validated);

        return $this->success($record, 'CPMK berhasil dibuat.', 201);
    }

    public function update(Request $request, string $record): JsonResponse
    {
        $cpmk = CapaianPembelajaranMataKuliah::where('id_cpmk', urldecode($record))->first();

        if (!$cpmk) {
            return $this->notFound('CPMK');
        }

        $validated = $request->validate([
            'id_cpl' => 'required|string|max:10',
            'desk_cpl' => 'required|string',
            'id_cpmk' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mst_cpmk', 'id_cpmk')->ignore($cpmk->id_cpmk, 'id_cpmk'),
            ],
            'desk_cpmk' => [
                'required',
                'string',
                Rule::unique('mst_cpmk', 'desk_cpmk')->ignore($cpmk->desk_cpmk, 'desk_cpmk'),
            ],
        ]);

        $oldDeskCpmk = $cpmk->desk_cpmk;
        $newDeskCpmk = $validated['desk_cpmk'];

        DB::transaction(function () use ($cpmk, $validated, $oldDeskCpmk, $newDeskCpmk) {
            if ($oldDeskCpmk !== $newDeskCpmk) {
                $cpmk->delete();
                CapaianPembelajaranMataKuliah::create($validated);
            } else {
                $cpmk->update($validated);
            }

            PemetaanCplCpmkMk::where('desk_cpmk', $oldDeskCpmk)->update([
                'id_cpl' => $validated['id_cpl'],
                'desk_cpl' => $validated['desk_cpl'],
                'id_cpmk' => $validated['id_cpmk'],
                'desk_cpmk' => $validated['desk_cpmk'],
            ]);

            PemetaanSubcpmk::where('desk_cpmk', $oldDeskCpmk)->update([
                'id_cpl' => $validated['id_cpl'],
                'id_cpmk' => $validated['id_cpmk'],
                'desk_cpmk' => $validated['desk_cpmk'],
            ]);
        });

        $freshRecord = CapaianPembelajaranMataKuliah::where('id_cpmk', $validated['id_cpmk'])->first();

        return $this->success($freshRecord, 'CPMK berhasil diperbarui.');
    }

    public function destroy(string $record): JsonResponse
    {
        $cpmk = CapaianPembelajaranMataKuliah::where('id_cpmk', urldecode($record))->first();

        if (!$cpmk) {
            return $this->notFound('CPMK');
        }

        $deskCpmk = $cpmk->desk_cpmk;
        $isUsedInPemetaan = PemetaanCplCpmkMk::where('desk_cpmk', $deskCpmk)->exists();
        $isUsedInSubCpmk = PemetaanSubcpmk::where('desk_cpmk', $deskCpmk)->exists();

        if ($isUsedInPemetaan || $isUsedInSubCpmk) {
            return $this->error('CPMK tidak dapat dihapus karena masih dipakai di pemetaan lain.', 409);
        }

        try {
            $cpmk->delete();

            return $this->deleted('CPMK berhasil dihapus.');
        } catch (QueryException $exception) {
            return $this->handleQueryException($exception, 'Gagal menghapus CPMK.');
        }
    }
}
