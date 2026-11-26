<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class CrudApiController extends BaseApiController
{
    abstract protected function modelClass(): string;

    abstract protected function storeRules(Request $request): array;

    abstract protected function updateRules(Request $request, Model $record): array;

    protected function resourceLabel(): string
    {
        return class_basename($this->modelClass());
    }

    protected function lookupColumn(): string
    {
        return $this->newModel()->getKeyName();
    }

    protected function defaultSorts(): array
    {
        return [];
    }

    protected function indexQuery(Request $request): Builder
    {
        return $this->newModel()->newQuery();
    }

    protected function transformRecord(Model $record): array
    {
        return $record->toArray();
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->indexQuery($request);

        foreach ($this->defaultSorts() as [$column, $direction]) {
            $query->orderBy($column, $direction);
        }

        $records = $query->get()->map(fn (Model $record) => $this->transformRecord($record));

        return $this->success($records, $this->resourceLabel() . ' berhasil diambil.');
    }

    public function show(string $record): JsonResponse
    {
        $model = $this->findRecord($record);

        if (!$model) {
            return $this->notFound($this->resourceLabel());
        }

        return $this->success($this->transformRecord($model), $this->resourceLabel() . ' berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->storeRules($request));
        $modelClass = $this->modelClass();
        $record = $modelClass::create($validated);

        return $this->success($this->transformRecord($record->fresh()), $this->resourceLabel() . ' berhasil dibuat.', 201);
    }

    public function update(Request $request, string $record): JsonResponse
    {
        $model = $this->findRecord($record);

        if (!$model) {
            return $this->notFound($this->resourceLabel());
        }

        $validated = $request->validate($this->updateRules($request, $model));
        $model->fill($validated);
        $model->save();

        return $this->success($this->transformRecord($model->fresh()), $this->resourceLabel() . ' berhasil diperbarui.');
    }

    public function destroy(string $record): JsonResponse
    {
        $model = $this->findRecord($record);

        if (!$model) {
            return $this->notFound($this->resourceLabel());
        }

        try {
            $model->delete();

            return $this->deleted($this->resourceLabel() . ' berhasil dihapus.');
        } catch (QueryException $exception) {
            return $this->handleQueryException($exception, 'Gagal menghapus ' . strtolower($this->resourceLabel()) . '.');
        }
    }

    protected function newModel(): Model
    {
        $class = $this->modelClass();

        return new $class();
    }

    protected function findRecord(string $value): ?Model
    {
        return $this->newModel()
            ->newQuery()
            ->where($this->lookupColumn(), urldecode($value))
            ->first();
    }
}
