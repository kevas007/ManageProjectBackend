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

        Route::get("/projects", [\App\Http\Controllers\Api\ProjectController::class, 'index']);
        Route::post("/project", [\App\Http\Controllers\Api\ProjectController::class, 'store']);
        Route::get("/project/{project}", [\App\Http\Controllers\Api\ProjectController::class, 'show']);
        Route::put("/project/{project}/update", [\App\Http\Controllers\Api\ProjectController::class, 'update']);
        Route::delete("/project/{project}/delete", [\App\Http\Controllers\Api\ProjectController::class, 'destroy']);
        Route::post("/project/{project}", [\App\Http\Controllers\Api\ProjectController::class, 'attachUser']);
    });

});
