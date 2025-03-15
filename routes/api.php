<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::prefix('/v1/')->group(function () {
    Route::post('/auth/login', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'login']);
    Route::post('/auth/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'register']);


    Route::middleware(['auth:sanctum'])->group(function () {

        Route::put('/users/{user}', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'update']);
        Route::delete('/delete/{user}', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'destroy']);
        Route::post('/logout/{user}', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'logout']);

    });

});
