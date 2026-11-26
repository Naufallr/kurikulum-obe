<?php

namespace App\Http\Controllers\Api;

use App\Models\TahapMekanisme;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahapMekanismeApiController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $records = TahapMekanisme::orderBy('id_cpl', 'asc')
            ->orderBy('id_mk', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        return $this->success($records, 'Tahap mekanisme penilaian berhasil diambil.');
    }

    public function show(string $record): JsonResponse
    {
        $tahap = TahapMekanisme::find(urldecode($record));

        if (!$tahap) {
            return $this->notFound('Tahap mekanisme penilaian');
        }

        return $this->success($tahap, 'Tahap mekanisme penilaian berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules($request));
        $record = TahapMekanisme::create($validated);
        $this->syncBobotMkByCplMk($record->id_cpl, $record->id_mk);

        return $this->success($record->fresh(), 'Tahap mekanisme penilaian berhasil dibuat.', 201);
    }

    public function update(Request $request, string $record): JsonResponse
    {
        $tahap = TahapMekanisme::find(urldecode($record));

        if (!$tahap) {
            return $this->notFound('Tahap mekanisme penilaian');
        }

        $validated = $request->validate($this->rules($request));
        $oldIdCpl = $tahap->id_cpl;
        $oldIdMk = $tahap->id_mk;

        $tahap->update($validated);

        $this->syncBobotMkByCplMk($oldIdCpl, $oldIdMk);
        $this->syncBobotMkByCplMk($tahap->id_cpl, $tahap->id_mk);

        return $this->success($tahap->fresh(), 'Tahap mekanisme penilaian berhasil diperbarui.');
    }

    public function destroy(string $record): JsonResponse
    {
        $tahap = TahapMekanisme::find(urldecode($record));

        if (!$tahap) {
            return $this->notFound('Tahap mekanisme penilaian');
        }

        try {
            $idCpl = $tahap->id_cpl;
            $idMk = $tahap->id_mk;
            $tahap->delete();
            $this->syncBobotMkByCplMk($idCpl, $idMk);

            return $this->deleted('Tahap mekanisme penilaian berhasil dihapus.');
        } catch (QueryException $exception) {
            return $this->handleQueryException($exception, 'Gagal menghapus tahap mekanisme penilaian.');
        }
    }

    private function syncBobotMkByCplMk(string $idCpl, string $idMk): void
    {
        $totalBobot = TahapMekanisme::where('id_cpl', $idCpl)
            ->where('id_mk', $idMk)
            ->sum('bobot');

        TahapMekanisme::where('id_cpl', $idCpl)
            ->where('id_mk', $idMk)
            ->update(['bobot_mk' => $totalBobot]);
    }

    private function rules(Request $request): array
    {
        return [
            'id_cpl' => 'required|string|max:10',
            'id_mk' => 'required|string|max:10',
            'id_cpmk' => [
                'required',
                'string',
                'max:20',
                Rule::exists('tbl_pemetaan_cpl_cpmk_mk', 'id_cpmk')->where(function ($query) use ($request) {
                    $query->where('id_cpl', $request->id_cpl)
                        ->where('id_mk', $request->id_mk);
                }),
            ],
            'tahap_penilaian' => 'required|string|max:255',
            'teknik_penilaian' => 'required|string|max:255',
            'instrumen' => 'required|string|max:255',
            'kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0',
        ];
    }
}
