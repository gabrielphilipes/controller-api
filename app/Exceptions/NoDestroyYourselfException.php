<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class NoDestroyYourselfException extends Exception
{
    protected $message = 'You can\'t destroy yourself.';

    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'error'   => class_basename($this),
            'message' => $this->getMessage(),
        ], 403);
    }
}
