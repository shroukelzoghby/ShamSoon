<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('successResponse')) {
    function successResponse($data = null, $message = null, int $statusCode = 200):\Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}

if (!function_exists('logError')) {
    function logError($message, \Exception $e): void
    {
        Log::error($message . ': ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode()
        ]);
    }

}
