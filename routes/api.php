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
        Route::get("/users", [\App\Http\Controllers\Auth\RegisteredUserController::class, 'allUser']);

        Route::get("/states", [\App\Http\Controllers\Api\StateController::class, 'index']);
        Route::post("/state/store", [\App\Http\Controllers\Api\StateController::class, 'store']);
        Route::get("/state/{store}", [\App\Http\Controllers\Api\StateController::class, 'show']);
        Route::put("/state/{store}/update", [\App\Http\Controllers\Api\StateController::class, 'update']);
        Route::delete("/state/{store}/delete", [\App\Http\Controllers\Api\StateController::class, 'destroy']);

    });

});
