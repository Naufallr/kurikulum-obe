<?php

namespace App\Http\Controllers\Api;

use App\Models\BahanKajian;
use App\Models\CapaianLulusanProdi;
use App\Models\MataKuliah;
use App\Models\ProfileLulusan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MappingMatrixApiController extends BaseApiController
{
    public function cplPlIndex(): JsonResponse
    {
        return $this->success($this->buildTwoColumnPayload(
            'tbl_pl_cpl',
            'id_cplp',
            'id_pl',
            CapaianLulusanProdi::orderBy('id', 'asc')->get(),
            ProfileLulusan::orderBy('id', 'asc')->get(),
            'cpls',
            'pls'
        ), 'Mapping CPL-PL berhasil diambil.');
    }

    public function cplPlReplace(Request $request): JsonResponse
    {
        $payload = $this->normalizeTwoColumnEntries($request, 'id_cplp', 'id_pl');
        $this->validateEntries($payload, [
            'entries.*.id_cplp' => 'required|string|exists:mst_cplp,id',
            'entries.*.id_pl' => 'required|string|exists:mst_pl,id',
        ]);
        $this->replaceTable('tbl_pl_cpl', $payload);

        return $this->success($payload, 'Mapping CPL-PL berhasil disimpan.');
    }

    public function cplBkIndex(): JsonResponse
    {
        return $this->success($this->buildTwoColumnPayload(
            'tbl_cpl_bk',
            'id_bk',
            'id_cpl',
            BahanKajian::orderBy('id', 'asc')->get(),
            CapaianLulusanProdi::orderBy('id', 'asc')->get(),
            'bks',
            'cpls'
        ), 'Mapping CPL-BK berhasil diambil.');
    }

    public function cplBkReplace(Request $request): JsonResponse
    {
        $payload = $this->normalizeTwoColumnEntries($request, 'id_bk', 'id_cpl');
        $this->validateEntries($payload, [
            'entries.*.id_bk' => 'required|string|exists:mst_bk,id',
            'entries.*.id_cpl' => 'required|string|exists:mst_cplp,id',
        ]);
        $this->replaceTable('tbl_cpl_bk', $payload);

        return $this->success($payload, 'Mapping CPL-BK berhasil disimpan.');
    }

    public function bkMkIndex(): JsonResponse
    {
        return $this->success($this->buildTwoColumnPayload(
            'tbl_bk_mk',
            'id_mk',
            'id_bk',
            MataKuliah::orderBy('id', 'asc')->get(),
            BahanKajian::orderBy('id', 'asc')->get(),
            'mks',
            'bks'
        ), 'Mapping BK-MK berhasil diambil.');
    }

    public function bkMkReplace(Request $request): JsonResponse
    {
        $payload = $this->normalizeTwoColumnEntries($request, 'id_mk', 'id_bk');
        $this->validateEntries($payload, [
            'entries.*.id_mk' => 'required|string|exists:mst_mk,id',
            'entries.*.id_bk' => 'required|string|exists:mst_bk,id',
        ]);
        $this->replaceTable('tbl_bk_mk', $payload);

        return $this->success($payload, 'Mapping BK-MK berhasil disimpan.');
    }

    public function cplMkIndex(): JsonResponse
    {
        return $this->success($this->buildTwoColumnPayload(
            'tbl_cpl_mk',
            'id_mk',
            'id_cpl',
            MataKuliah::orderBy('id', 'asc')->get(),
            CapaianLulusanProdi::orderBy('id', 'asc')->get(),
            'mks',
            'cpls'
        ), 'Mapping CPL-MK berhasil diambil.');
    }

    public function cplMkReplace(Request $request): JsonResponse
    {
        $payload = $this->normalizeTwoColumnEntries($request, 'id_mk', 'id_cpl');
        $this->validateEntries($payload, [
            'entries.*.id_mk' => 'required|string|exists:mst_mk,id',
            'entries.*.id_cpl' => 'required|string|exists:mst_cplp,id',
        ]);
        $this->replaceTable('tbl_cpl_mk', $payload);

        return $this->success($payload, 'Mapping CPL-MK berhasil disimpan.');
    }

    public function cplBkMkIndex(): JsonResponse
    {
        $entries = DB::table('tbl_cpl_bk_mk')
            ->orderBy('id_cpl', 'asc')
            ->orderBy('id_bk', 'asc')
            ->orderBy('id_mk', 'asc')
            ->get();

        $selected = [];
        foreach ($entries as $entry) {
            $selected[$entry->id_cpl][$entry->id_bk] = $entry->id_mk;
        }

        return $this->success([
            'entries' => $entries,
            'selected' => $selected,
            'references' => [
                'cpls' => CapaianLulusanProdi::orderBy('id', 'asc')->get(),
                'bks' => BahanKajian::orderBy('id', 'asc')->get(),
                'mks' => MataKuliah::orderBy('id', 'asc')->get(),
            ],
        ], 'Mapping CPL-BK-MK berhasil diambil.');
    }

    public function cplBkMkReplace(Request $request): JsonResponse
    {
        $payload = $this->normalizeThreeColumnEntries($request);
        $this->validateEntries($payload, [
            'entries.*.id_cpl' => 'required|string|exists:mst_cplp,id',
            'entries.*.id_bk' => 'required|string|exists:mst_bk,id',
            'entries.*.id_mk' => 'required|string|exists:mst_mk,id',
        ]);
        $this->replaceTable('tbl_cpl_bk_mk', $payload);

        return $this->success($payload, 'Mapping CPL-BK-MK berhasil disimpan.');
    }

    private function buildTwoColumnPayload(
        string $table,
        string $rowKey,
        string $columnKey,
        mixed $rows,
        mixed $columns,
        string $rowLabel,
        string $columnLabel
    ): array {
        $entries = DB::table($table)
            ->orderBy($rowKey, 'asc')
            ->orderBy($columnKey, 'asc')
            ->get();

        $matrix = [];
        foreach ($entries as $entry) {
            $matrix[$entry->{$rowKey}][$entry->{$columnKey}] = true;
        }

        return [
            'entries' => $entries,
            'matrix' => $matrix,
            'references' => [
                $rowLabel => $rows,
                $columnLabel => $columns,
            ],
        ];
    }

    private function normalizeTwoColumnEntries(Request $request, string $firstKey, string $secondKey): array
    {
        if ($request->filled('entries')) {
            return collect($request->input('entries', []))
                ->map(fn ($entry) => [
                    $firstKey => $entry[$firstKey] ?? null,
                    $secondKey => $entry[$secondKey] ?? null,
                ])
                ->filter(fn ($entry) => !empty($entry[$firstKey]) && !empty($entry[$secondKey]))
                ->values()
                ->all();
        }

        $matrix = $request->input('matrix', []);
        $payload = [];

        foreach ($matrix as $firstValue => $items) {
            foreach ($items as $secondValue => $value) {
                if ($value) {
                    $payload[] = [
                        $firstKey => $firstValue,
                        $secondKey => $secondValue,
                    ];
                }
            }
        }

        return $payload;
    }

    private function normalizeThreeColumnEntries(Request $request): array
    {
        if ($request->filled('entries')) {
            return collect($request->input('entries', []))
                ->map(fn ($entry) => [
                    'id_cpl' => $entry['id_cpl'] ?? null,
                    'id_bk' => $entry['id_bk'] ?? null,
                    'id_mk' => $entry['id_mk'] ?? null,
                ])
                ->filter(fn ($entry) => !empty($entry['id_cpl']) && !empty($entry['id_bk']) && !empty($entry['id_mk']))
                ->values()
                ->all();
        }

        $mapping = $request->input('mapping', []);
        $payload = [];

        foreach ($mapping as $cplId => $bkItems) {
            foreach ($bkItems as $bkId => $mkSelection) {
                if (is_array($mkSelection)) {
                    foreach ($mkSelection as $mkId => $value) {
                        if ($value) {
                            $payload[] = [
                                'id_cpl' => $cplId,
                                'id_bk' => $bkId,
                                'id_mk' => $mkId,
                            ];
                        }
                    }
                    continue;
                }

                if (!empty($mkSelection)) {
                    $payload[] = [
                        'id_cpl' => $cplId,
                        'id_bk' => $bkId,
                        'id_mk' => $mkSelection,
                    ];
                }
            }
        }

        return $payload;
    }

    private function validateEntries(array $payload, array $rules): void
    {
        Validator::make(['entries' => $payload], $rules)->validate();
    }

    private function replaceTable(string $table, array $payload): void
    {
        DB::transaction(function () use ($table, $payload) {
            DB::table($table)->delete();

            if (!empty($payload)) {
                DB::table($table)->insert($payload);
            }
        });
    }
}
