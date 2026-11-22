<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function error(string $code, string $message, int $status, ?array $details = null, array $headers = []): JsonResponse
    {
        $error = ['code' => $code, 'message' => $message];

        if ($details !== null) {
            $error['details'] = $details;
        }

        return response()->json(['error' => $error], $status, $headers);
    }
}