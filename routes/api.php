<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\ActorController;
use App\Http\Controllers\Api\DirectorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// con only ci assicuriamo che vengano generate solo le rotte per le funzioni che vogliamo
Route::apiResource('movies', MovieController::class)->only(['index', 'show']);
Route::apiResource('genres', GenreController::class)->only(['index', 'show']);
Route::apiResource('actors', ActorController::class)->only(['index', 'show']);
Route::apiResource('directors', DirectorController::class)->only(['index', 'show']);
