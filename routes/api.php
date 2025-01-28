<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\SportsController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SubkategoriController;
use App\Http\Controllers\KontenController;
use App\Http\Controllers\PertandinganController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckAdminVenue;
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

//Superadmin Kelola Venue
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('venue')
    ->group(function () {
        Route::post('/buat', [VenueController::class, 'buatVenue']);
        Route::get('/semua', [VenueController::class, 'ambilSemuaVenue']);
        Route::put('/ubah/{id}', [VenueController::class, 'ubahVenue']);
        Route::put('/ubah-status/{id}', [VenueController::class, 'ubahStatus']);
        Route::delete('/hapus/{id}', [VenueController::class, 'hapusVenue']);
    });

//Superadmin Kelola Pertandingan
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('pertandingan')
    ->group(function () {
        Route::post('/buat', [PertandinganController::class, 'buatPertandingan']);
        Route::get('/semua', [PertandinganController::class, 'ambilSemuaPertandingan']);
        Route::put('/ubah/{id}', [PertandinganController::class, 'ubahPertandingan']);
        Route::put('/ubah-status/{id}', [PertandinganController::class, 'ubahStatus']);
        Route::delete('/hapus/{id}', [PertandinganController::class, 'hapusPertandingan']);
    });

Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('user')
    ->group(function () {
        Route::get('/semua-user', [UserController::class, 'ambilSemuaUser']);
        Route::put('/ubah-user/{id}', [UserController::class, 'ubahUser']);
    });


Route::middleware(['auth:sanctum', CheckAdminVenue::class])
    // Admin Venue
    ->prefix('venue')
    ->group(function () {
        Route::post('/{venueId}/tambah-pertandingan', [VenueController::class, 'tambahkanPertandinganKeVenue']);
        Route::get('/{venueId}/ambil-pertandingan', [VenueController::class, 'getPertandinganDariVenue']);
        Route::delete('/{venueId}/hapus-pertandingan', [VenueController::class, 'hapusPertandinganDariVenue']);
        Route::put('/{venueId}/kelola-profile', [VenueController::class, 'kelolaProfileAdmin']);
    });


// General semua bisa akses
// Venue
Route::prefix('venue')->group(function () {
    Route::get('/semua-venue-aktif', [VenueController::class, 'semuaVenueAktif']);
    Route::get('/pertandingan/{pertandinganId}', [VenueController::class, 'getVenueByPertandingan']);
    Route::get('/city/{city}', [VenueController::class, 'getVenueByCity']);
    Route::get('/detail/{id}', [VenueController::class, 'detailVenue']);
});

Route::prefix('pertandingan')->group(function () {
    Route::get('/semua-pertandingan-aktif', [PertandinganController::class, 'semuaPertandinganAktif']);
    Route::get('/detail/{id}', [PertandinganController::class, 'detailPertandingan']);
});






















Route::prefix('sports')->group(function () {
    Route::get('/categories', [SportsController::class, 'getCategories']); // Dapatkan kategori
    Route::get('/{sport}/leagues', [SportsController::class, 'getLeaguesByCategory']); // Dapatkan liga berdasarkan olahraga
    Route::get('/{sport}/teams', [SportsController::class, 'getTeamsByLeague']); // Dapatkan tim berdasarkan liga
    Route::post('/schedule', [SportsController::class, 'createSchedule']); // Buat jadwal
    Route::get('/{sport}/fixtures', [SportsController::class, 'getFixturesBySeason']);
});













//Kategori
Route::get('/semua-kategori', [KategoriController::class, 'ambilSemuaKategori']);
Route::get('/detail-kategori/{slug}', [KategoriController::class, 'ambilDetailKategori']);


Route::prefix('kategori')->group(function () {
    Route::get('/', [KategoriController::class, 'semuaKategori']);
    Route::post('/', [KategoriController::class, 'tambahKategori']);
    Route::get('/{id}', [KategoriController::class, 'rincianKategori']);
    Route::put('/{id}', [KategoriController::class, 'ubahKategori']);
    Route::delete('/{id}', [KategoriController::class, 'hapusKategori']);
});

Route::prefix('subkategori')->group(function () {
    Route::get('/', [SubkategoriController::class, 'semuaSubkategori']);
    Route::post('/', [SubkategoriController::class, 'tambahSubkategori']);
    Route::get('/{id}', [SubkategoriController::class, 'rincianSubkategori']);
    Route::get('/kategori/{kategoriId}', [SubkategoriController::class, 'subkategoriBerdasarkanKategori']);
    Route::put('/{id}', [SubkategoriController::class, 'ubahSubkategori']);
    Route::delete('/{id}', [SubkategoriController::class, 'hapusSubkategori']);
});
