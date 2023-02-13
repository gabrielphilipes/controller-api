<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class TokenExpiredException extends Exception
{
    protected $message = 'Token has expired.';

    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error'   => class_basename($this),
            'message' => $this->getMessage(),
        ], 401);
    }
}
