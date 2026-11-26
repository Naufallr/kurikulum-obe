<?php

namespace App\Http\Controllers\Api;

use App\Models\CapaianLulusanProdi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CapaianLulusanProdiApiController extends CrudApiController
{
    protected function modelClass(): string
    {
        return CapaianLulusanProdi::class;
    }

    protected function resourceLabel(): string
    {
        return 'Capaian Lulusan Prodi';
    }

    protected function defaultSorts(): array
    {
        return [['id', 'asc']];
    }

    protected function storeRules(Request $request): array
    {
        return [
            'id' => 'required|string|max:5|unique:mst_cplp,id',
            'desk' => 'required|string|unique:mst_cplp,desk',
            'aspek' => 'nullable|string|max:255',
        ];
    }

    protected function updateRules(Request $request, Model $record): array
    {
        return [
            'id' => [
                'required',
                'string',
                'max:5',
                Rule::unique('mst_cplp', 'id')->ignore($record->id, 'id'),
            ],
            'desk' => [
                'required',
                'string',
                Rule::unique('mst_cplp', 'desk')->ignore($record->id, 'id'),
            ],
            'aspek' => 'nullable|string|max:255',
        ];
    }
}
