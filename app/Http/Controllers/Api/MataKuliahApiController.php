<?php

namespace App\Http\Controllers\Api;

use App\Models\MataKuliah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataKuliahApiController extends CrudApiController
{
    protected function modelClass(): string
    {
        return MataKuliah::class;
    }

    protected function resourceLabel(): string
    {
        return 'Mata Kuliah';
    }

    protected function lookupColumn(): string
    {
        return 'id';
    }

    protected function defaultSorts(): array
    {
        return [
            ['semester', 'asc'],
            ['id', 'asc'],
        ];
    }

    protected function indexQuery(Request $request): Builder
    {
        $query = MataKuliah::query();

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->query('tahun'));
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->query('semester'));
        }

        return $query;
    }

    protected function storeRules(Request $request): array
    {
        return [
            'id' => 'required|string|max:10|unique:mst_mk,id',
            'nama_mk' => 'required|string|max:255|unique:mst_mk,nama_mk',
            'sks' => 'required|integer|min:0',
            'jenis_mk' => 'required|string|max:255',
            'status' => 'required|string|max:30',
            'tahun' => 'required|integer',
            'semester' => 'required|integer|between:1,8',
        ];
    }

    protected function updateRules(Request $request, Model $record): array
    {
        return [
            'id' => [
                'required',
                'string',
                'max:10',
                Rule::unique('mst_mk', 'id')->ignore($record->id, 'id'),
            ],
            'nama_mk' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mst_mk', 'nama_mk')->ignore($record->nama_mk, 'nama_mk'),
            ],
            'sks' => 'required|integer|min:0',
            'jenis_mk' => 'required|string|max:255',
            'status' => 'required|string|max:30',
            'tahun' => 'required|integer',
            'semester' => 'required|integer|between:1,8',
        ];
    }
}
