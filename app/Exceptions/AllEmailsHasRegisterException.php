<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AllEmailsHasRegisterException extends Exception
{
    protected $message = 'All email already registers.';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error'   => class_basename($this),
            'message' => $this->getMessage(),
        ], 400);
    }
}
