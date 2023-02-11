<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnkownException extends Exception
{
    protected $message = 'Unknown error';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error'   => class_basename($this),
            'message' => $this->getMessage(),
        ], 418);
    }
}
