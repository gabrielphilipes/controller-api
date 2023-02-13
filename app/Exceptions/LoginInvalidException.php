<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class LoginInvalidException extends Exception
{
    protected $message = 'Email or password is incorrect';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error'   => class_basename($this),
            'message' => $this->getMessage(),
        ], 401);
    }
}
