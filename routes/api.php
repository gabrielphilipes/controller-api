<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DebugController;

Route::get('/version', fn () => ['version' => '1.0.0']);
Route::middleware('auth:sanctum')->match(['get', 'post'], '/debug', [DebugController::class, 'debug']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    /**
     * USER
     * Manager users
     */
    Route::get('users/me', [UserController::class, 'me']);
    Route::post('users/check-email', [UserController::class, 'checkEmail']);
    Route::post('users/multiple', [UserController::class, 'storeMultiple']);
    Route::resource('users', UserController::class);

    // ...
});

require __DIR__ . '/api/auth.php';
