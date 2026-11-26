<?php

namespace App\Http\Controllers\Api;

use App\Models\BobotPenilaian;
use App\Models\MataKuliah;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BobotPenilaianApiController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $records = BobotPenilaian::orderBy('id_mk', 'asc')
            ->orderBy('id_cpl', 'asc')
            ->orderBy('id_cpmk', 'asc')
            ->get();

        return $this->success($records, 'Bobot penilaian berhasil diambil.');
    }

    public function show(string $record): JsonResponse
    {
        $bobot = BobotPenilaian::find(urldecode($record));

        if (!$bobot) {
            return $this->notFound('Bobot penilaian');
        }

        return $this->success($bobot, 'Bobot penilaian berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $record = BobotPenilaian::create($this->buildPayload($validated));
        $this->syncTotalNilaiMk($record->id_mk);

        return $this->success($record->fresh(), 'Bobot penilaian berhasil dibuat.', 201);
    }

    public function update(Request $request, string $record): JsonResponse
    {
        $bobot = BobotPenilaian::find(urldecode($record));

        if (!$bobot) {
            return $this->notFound('Bobot penilaian');
        }

        $validated = $request->validate($this->rules());
        $oldIdMk = $bobot->id_mk;

        $bobot->update($this->buildPayload($validated));

        $this->syncTotalNilaiMk($oldIdMk);
        $this->syncTotalNilaiMk($bobot->id_mk);

        return $this->success($bobot->fresh(), 'Bobot penilaian berhasil diperbarui.');
    }

    public function destroy(string $record): JsonResponse
    {
        $bobot = BobotPenilaian::find(urldecode($record));

        if (!$bobot) {
            return $this->notFound('Bobot penilaian');
        }

        try {
            $idMk = $bobot->id_mk;
            $bobot->delete();
            $this->syncTotalNilaiMk($idMk);

            return $this->deleted('Bobot penilaian berhasil dihapus.');
        } catch (QueryException $exception) {
            return $this->handleQueryException($exception, 'Gagal menghapus bobot penilaian.');
        }
    }

    private function syncTotalNilaiMk(string $idMk): void
    {
        $totalNilaiMk = BobotPenilaian::where('id_mk', $idMk)->sum('total');

        BobotPenilaian::where('id_mk', $idMk)->update([
            'total_nilai_mk' => $totalNilaiMk,
        ]);
    }

    private function buildPayload(array $validated): array
    {
        $mataKuliah = MataKuliah::where('id', $validated['id_mk'])->first();

        $total = ($validated['partisipasi'] ?? 0)
            + ($validated['observasi'] ?? 0)
            + ($validated['unjuk_kerja'] ?? 0)
            + ($validated['tes_uts'] ?? 0)
            + ($validated['tes_uas'] ?? 0)
            + ($validated['tes_lisan'] ?? 0);

        $validated['nama_mk'] = $mataKuliah?->nama_mk ?? '';
        $validated['total'] = $total;

        return $validated;
    }

    private function rules(): array
    {
        return [
            'id_mk' => 'required|string|max:10|exists:mst_mk,id',
            'id_cpl' => 'required|string|max:10',
            'id_cpmk' => 'required|string|max:10',
            'mbkm' => 'nullable|string|max:255',
            'partisipasi' => 'nullable|numeric|min:0|max:100',
            'observasi' => 'nullable|numeric|min:0|max:100',
            'unjuk_kerja' => 'nullable|numeric|min:0|max:100',
            'tes_uts' => 'nullable|numeric|min:0|max:100',
            'tes_uas' => 'nullable|numeric|min:0|max:100',
            'tes_lisan' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
