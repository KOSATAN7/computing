<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\SportsController;
use App\Http\Controllers\PertandinganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Middleware\CheckAdminVenue;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckSuperAdmin;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// Pendaftaran Akun
Route::post('/register-54', [AuthController::class, 'daftarSuperAdmin']);
Route::post('/register-infobar', [AuthController::class, 'daftarInfobar']);

Route::middleware([EnsureFrontendRequestsAreStateful::class])->group(function () {
    Route::post('/login', [AuthController::class, 'masuk']);
});

Route::middleware(['auth:sanctum'])->get('/check-login', [AuthController::class, 'cekMasuk']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'keluar']);
});

// Superadmin | Kelola Venue
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('venue')
    ->group(function () {
        Route::post('/buat', [VenueController::class, 'buatVenue']);
        Route::get('/semua', [VenueController::class, 'ambilSemuaVenue']);
        Route::put('/ubah/{id}', [VenueController::class, 'ubahVenue']);
        Route::put('/ubah-status/{id}', [VenueController::class, 'ubahStatus']);
        Route::delete('/hapus/{id}', [VenueController::class, 'hapusVenue']);
    });

// Superadmin | Kelola Pertandingan
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('pertandingan')
    ->group(function () {
        Route::post('/buat', [PertandinganController::class, 'buatPertandingan']);
        Route::get('/semua', [PertandinganController::class, 'ambilSemuaPertandingan']);
        Route::put('/ubah/{id}', [PertandinganController::class, 'ubahPertandingan']);
        Route::put('/ubah-status/{id}', [PertandinganController::class, 'ubahStatus']);
        Route::delete('/hapus/{id}', [PertandinganController::class, 'hapusPertandingan']);
    });

// Superadmin | Kelola Pengguna
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('user')
    ->group(function () {
        Route::get('/semua-user', [UserController::class, 'ambilSemuaPengguna']);
        Route::get('/user-by-id/{id}', [UserController::class, 'ambilPenggunaBerdasarkanId']);
        Route::put('/ubah-user/{id}', [UserController::class, 'ubahPengguna']);
        Route::delete('/hapus-user/{id}', [UserController::class, 'hapusPengguna']);
    });

// Admin Venue 
Route::middleware(['auth:sanctum', CheckAdminVenue::class])
    ->prefix('venue')
    ->group(function () {
        Route::post('/{venueId}/tambah-pertandingan', [VenueController::class, 'tambahPertandinganKeVenue']);
        Route::get('/{venueId}/ambil-pertandingan', [VenueController::class, 'ambilPertandinganDariVenue']);
        Route::delete('/{venueId}/hapus-pertandingan', [VenueController::class, 'hapusPertandinganDariVenue']);
        Route::put('/{venueId}/kelola-profile', [VenueController::class, 'kelolaProfilAdmin']);
        Route::get('/detail-venue/{venueId}', [VenueController::class, 'ambilVenueBerdasarkanId']);
    });

// Venue - Umum
Route::prefix('venue')->group(function () {
    Route::get('/semua-venue-aktif', [VenueController::class, 'ambilSemuaVenueAktif']);
    Route::get('/pertandingan/{pertandinganId}', [VenueController::class, 'ambilVenueBerdasarkanPertandingan']);
    Route::get('/city/{city}', [VenueController::class, 'ambilVenueBerdasarkanKota']);
    Route::get('/detail/{id}', [VenueController::class, 'detailVenue']);
});

// Olahraga - Umum
Route::prefix('sports')->group(function () {
    Route::get('/categories', [SportsController::class, 'ambilKategori']);
    Route::get('/{sport}/countries', [SportsController::class, 'ambilNegaraBerdasarkanKategori']);
    Route::get('/{sport}/leagues', [SportsController::class, 'ambilLigaBerdasarkanKategoriNegaraMusim']);
    Route::get('/{sport}/teams', [SportsController::class, 'ambilTimBerdasarkanLiga']);
    Route::post('/schedule', [SportsController::class, 'buatJadwal']);
    Route::get('/{sport}/fixtures', [SportsController::class, 'ambilPertandinganBerdasarkanMusim']);
});

// Pertandingan - Umum
Route::prefix('pertandingan')->group(function () {
    Route::get('/semua-pertandingan-aktif', [PertandinganController::class, 'ambilSemuaPertandinganAktif']);
    Route::get('/detail/{id}', [PertandinganController::class, 'ambilDetailPertandingan']);
});

// Menu - Umum
Route::prefix('menu/venue/{venueId}')->controller(MenuController::class)->group(function () {
    Route::get('/menu-by-venue', 'ambilMenuBerdasarkanVenue');
    Route::get('/menu-aktif', 'menuAktifBerdasarkanVenue');
    Route::get('/detail-menu/{menuId}', 'ambilDetailMenu');
    Route::post('/buat', 'tambahMenu');
    Route::put('/ubah-menu/{menuId}', 'ubahMenu');
    Route::delete('/hapus-menu/{menuId}', 'hapusMenu');
});

// Pengambilan File Venue
Route::get('/{filename}', function ($filename) {
    $path = public_path('storage/venues/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});
