<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/version', fn() => ['version' => '1.0.0']);

/**
 * USER
 * Manager users
 */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('users/me', [\App\Http\Controllers\AuthController::class, 'me']);
    Route::resource('users', \App\Http\Controllers\UserController::class);
});

require __DIR__ . '/api/auth.php';
