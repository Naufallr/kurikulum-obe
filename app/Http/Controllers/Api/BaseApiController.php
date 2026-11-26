<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function notFound(string $resource = 'Resource'): JsonResponse
    {
        return $this->error($resource . ' tidak ditemukan.', 404);
    }

    protected function deleted(string $message = 'Data berhasil dihapus.'): JsonResponse
    {
        return $this->success(null, $message);
    }

    protected function handleQueryException(QueryException $exception, string $fallbackMessage): JsonResponse
    {
        if (str_contains($exception->getMessage(), '23503') || str_contains($exception->getMessage(), 'foreign key constraint')) {
            return $this->error('Data tidak dapat diproses karena masih digunakan di tabel lain.', 409);
        }

        return $this->error($fallbackMessage, 500, [
            'database' => $exception->getMessage(),
        ]);
    }
}
