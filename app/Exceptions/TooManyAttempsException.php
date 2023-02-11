<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class TooManyAttempsException extends Exception
{
    protected $message = 'Too many attempts. Please try again later.';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error'   => class_basename($this),
            'message' => $this->getMessage(),
        ], 429);
    }
}
