<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ModelNotFoundException extends Exception
{
    protected $message = 'Model not found.';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error'   => class_basename($this),
            'message' => $this->getMessage(),
        ], 404);
    }
}
