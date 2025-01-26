<?php

use App\Http\Controllers\VenueController;
use App\Http\Controllers\FilmsController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckSuperAdmin;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;


Route::post('/register-54', [AuthController::class, 'registerSuperAdmin']);
Route::post('/register-infobar', [AuthController::class, 'registerInfobar']);


Route::middleware([EnsureFrontendRequestsAreStateful::class])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->get('/check-login', [AuthController::class, 'checkLogin']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
});

Route::middleware(['auth:sanctum', CheckSuperAdmin::class])->group(function () {
    Route::post('/venue', [VenueController::class, 'createVenueWithAdmin']);
    Route::put('/venue/{id}', [VenueController::class, 'update']);
});

Route::middleware(['auth:sanctum'])->get('/list-venue', [VenueController::class, 'index']);

Route::get('/venue/{id}', [VenueController::class, 'show']);
Route::post('/venue-by-film/{filmId}', [VenueController::class, 'addFilmsToVenue']);
Route::get('/venues-by-film/{filmId}', [VenueController::class, 'getVenuesByFilm']);

Route::get('/film', [FilmsController::class, 'index']);
Route::post('/film', [FilmsController::class, 'store']);
Route::get('/film/{id}', [FilmsController::class, 'show']);
Route::get('/film-by-kategori/{id}', [FilmsController::class, 'showByKategori']);
Route::put('/film/{id}', [FilmsController::class, 'update']);
Route::delete('/film/{id}', [FilmsController::class, 'delete']);

Route::get('/kategori', [KategoriController::class, 'index']);
Route::post('/kategori', [KategoriController::class, 'store']);
Route::put('/kategori/{id}', [KategoriController::class, 'update']);
Route::delete('/kategori/{id}', [KategoriController::class, 'delete']);
