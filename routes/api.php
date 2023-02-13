<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/version', fn() => ['version' => '1.0.0']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__ . '/api/auth.php';
