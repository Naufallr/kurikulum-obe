<?php

namespace App\Http\Controllers\Api;

use App\Models\ProfileLulusan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileLulusanApiController extends CrudApiController
{
    protected function modelClass(): string
    {
        return ProfileLulusan::class;
    }

    protected function resourceLabel(): string
    {
        return 'Profile Lulusan';
    }

    protected function defaultSorts(): array
    {
        return [['id', 'asc']];
    }

    protected function storeRules(Request $request): array
    {
        return [
            'id' => 'required|string|max:4|unique:mst_pl,id',
            'desk' => 'required|string|max:255|unique:mst_pl,desk',
            'unsur' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }

    protected function updateRules(Request $request, Model $record): array
    {
        return [
            'id' => [
                'required',
                'string',
                'max:4',
                Rule::unique('mst_pl', 'id')->ignore($record->id, 'id'),
            ],
            'desk' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mst_pl', 'desk')->ignore($record->id, 'id'),
            ],
            'unsur' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }
}
