<?php

namespace App\Http\Controllers\Api;

use App\Models\BahanKajian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BahanKajianApiController extends CrudApiController
{
    protected function modelClass(): string
    {
        return BahanKajian::class;
    }

    protected function resourceLabel(): string
    {
        return 'Bahan Kajian';
    }

    protected function defaultSorts(): array
    {
        return [['id', 'asc']];
    }

    protected function storeRules(Request $request): array
    {
        return [
            'id' => 'required|string|max:10|unique:mst_bk,id',
            'bahan_kajian' => 'required|string|max:255|unique:mst_bk,bahan_kajian',
            'kategori' => 'required|string|max:255',
            'referensi' => 'required|string|max:255',
        ];
    }

    protected function updateRules(Request $request, Model $record): array
    {
        return [
            'id' => [
                'required',
                'string',
                'max:10',
                Rule::unique('mst_bk', 'id')->ignore($record->id, 'id'),
            ],
            'bahan_kajian' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mst_bk', 'bahan_kajian')->ignore($record->id, 'id'),
            ],
            'kategori' => 'required|string|max:255',
            'referensi' => 'required|string|max:255',
        ];
    }
}
