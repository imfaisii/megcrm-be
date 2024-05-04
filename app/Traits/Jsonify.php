<?php

namespace App\Traits;

trait Jsonify
{
    public function success($message = 'Success', $data = null, $statusCode = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }

    public function error($message = 'Error', $statusCode = 400)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }

    public function exception($exception, $statusCode = 500)
    {
        $response = [
            'success' => false,
            'message' => $exception->getMessage(),
        ];

        return response()->json($response, $statusCode);
    }
}
