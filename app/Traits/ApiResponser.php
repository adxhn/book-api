<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    public function success(?string $message = null, $data = null, int $code = 200): JsonResponse
    {
        return response()->json(array_filter([
            'message' => $message,
            'data'    => $data,
        ]), $code);
    }

    public function noContent(): \Illuminate\Http\Response
    {
        return response()->noContent();
    }
}
