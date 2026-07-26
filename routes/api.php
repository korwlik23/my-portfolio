<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'throttle:api-read'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->middleware('throttle:api-write');
        Route::get('me', [AuthController::class, 'me']);
    });
});
