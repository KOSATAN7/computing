<?php

use App\Http\Controllers\VenueController;
use App\Http\Controllers\FilmsController;
use Illuminate\Support\Facades\Route;

Route::get('/venue', [VenueController::class, 'index']);
Route::post('/venue', [VenueController::class, 'store']);
Route::get('/venue/{id}', [VenueController::class, 'show']);
Route::put('/venue/{id}', [VenueController::class, 'update']);
Route::delete('/venue/{id}', [VenueController::class, 'delete']);

Route::get('/film', [FilmsController::class, 'index']);
Route::post('/film', [FilmsController::class, 'store']);
Route::get('/film/{id}', [FilmsController::class, 'show']);
Route::put('/film/{id}', [FilmsController::class, 'update']);
Route::delete('/film/{id}', [FilmsController::class, 'delete']);