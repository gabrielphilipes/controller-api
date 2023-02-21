<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::get('new-password/{token}', [AuthController::class, 'checkToken']);
    Route::post('new-password/{token}', [AuthController::class, 'newPassword']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('increase-token-lifetime', [AuthController::class, 'increaseTokenLifetime']);
    });
});
