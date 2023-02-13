<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class LogoutException extends Exception
{
    protected $message = 'Error in logout';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error'   => class_basename($this),
            'message' => $this->getMessage(),
        ], 500);
    }
}
