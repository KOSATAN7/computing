<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProviderPembayaranController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\SportsController;
use App\Http\Controllers\PertandinganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MetodePembayaranController;
use App\Http\Controllers\BookingController;
use App\Http\Middleware\CheckAdminVenue;
use App\Http\Middleware\CheckInfobar;
use App\Http\Middleware\CheckSuperAdmin;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// Autentikasi
Route::post('/register-superadmin', [AuthController::class, 'daftarSuperAdmin']);
Route::post('/register-infobar', [AuthController::class, 'daftarInfobar']);

Route::middleware([EnsureFrontendRequestsAreStateful::class])->group(function () {
    Route::post('/login', [AuthController::class, 'masuk']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/check-login', [AuthController::class, 'cekMasuk']);
    Route::post('/logout', [AuthController::class, 'keluar']);
});

// Superadmin | Kelola Venue
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('venue')
    ->controller(VenueController::class)
    ->group(function () {
        Route::post('/', 'buatVenue');
        Route::get('/', 'ambilSemuaVenue');
        Route::put('/{id}', 'ubahVenue');
        Route::patch('/status/{id}', 'ubahStatus');
        Route::delete('/{id}', 'hapusVenue');
    });

// Superadmin | Kelola Pertandingan
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('pertandingan')
    ->controller(PertandinganController::class)
    ->group(function () {
        Route::post('/', 'buatPertandingan');
        Route::get('/', 'ambilSemuaPertandingan');
        Route::put('/{id}', 'ubahPertandingan');
        Route::patch('/status/{id}', 'ubahStatus');
        Route::delete('/{id}', 'hapusPertandingan');
    });

// Superadmin | Kelola Pengguna
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('user')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/', 'ambilSemuaPengguna');
        Route::get('/{id}', 'ambilPenggunaBerdasarkanId');
        Route::put('/{id}', 'ubahPengguna');
        Route::delete('/{id}', 'hapusPengguna');
    });

// Superadmin | Kelola Metode Pembayaran
Route::middleware(['auth:sanctum', CheckSuperAdmin::class])
    ->prefix('metode-pembayaran')
    ->controller(MetodePembayaranController::class)
    ->group(function () {
        Route::get('/', 'ambilMetodePembayaran');
        Route::get('/{id}', 'detailMetodePembayaran');
        Route::post('/', 'buatMetodePembayaran');
        Route::put('/{id}', 'ubahMetodePembayaran');
        Route::patch('/{id}/status', 'ubahStatusMetodePembayaran');
        Route::delete('/{id}', 'hapusMetodePembayaran');
    });




// ----------------------------------------------------------------------------------------------------------------
// Admin Venue | Kelola Profil
Route::middleware(['auth:sanctum', CheckAdminVenue::class])
    ->prefix('profil/venue')
    ->controller(VenueController::class)
    ->group(function () {
        Route::put('/{venueId}', 'kelolaProfilAdmin');
        Route::get('/{venueId}', 'ambilVenueBerdasarkanId');
    });

// Admin Venue | Kelola Konten
Route::middleware(['auth:sanctum', CheckAdminVenue::class])
    ->prefix('konten/venue')
    ->controller(VenueController::class)
    ->group(function () {
        Route::post('/{venueId}', 'tambahPertandinganKeVenue');
        Route::get('/{venueId}', 'ambilPertandinganDariVenue');
        Route::get('/{venueId}/{pertandinganId}', 'ambilPertandinganDariVenueById');
        Route::delete('/{venueId}', 'hapusPertandinganDariVenue');
    });


// Admin Venue | Kelola Menu
Route::middleware(['auth:sanctum', CheckAdminVenue::class])
    ->prefix('menu/venue/{venueId}')
    ->controller(MenuController::class)
    ->group(function () {
        Route::post('/', 'tambahMenu');
        Route::get('/', 'ambilMenuBerdasarkanVenue');
        Route::get('/{menuId}', 'ambilDetailMenu');
        Route::put('/{menuId}', 'ubahMenu');
        Route::patch('/status/{menuId}', 'ubahStatusMenu');
        Route::delete('/{menuId}', 'hapusMenu');
    });

// Admin Venue | Kelola Provider
Route::middleware(['auth:sanctum', CheckAdminVenue::class])
    ->prefix('venue/{venueId}/provider')
    ->controller(ProviderPembayaranController::class)
    ->group(function () {
        Route::post('/',  'buatProviderPembayaran');
        Route::get('/{id}',  'detailProviderPembayaran');
        Route::put('/{id}',  'ubahProviderPembayaran');
        Route::patch('/{id}/status',  'ubahStatusProviderPembayaran');
        Route::delete('/{id}', 'hapusProviderPembayaran');
    });

    Route::middleware('auth:sanctum')
    ->prefix('venue/{venueId}/provider')
    ->controller(ProviderPembayaranController::class)
    ->group(function () {
        Route::get('/',  'ambilProviderPembayaran');
    });

// Admin Venue | get data metode pembayaran
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/list-metode', [MetodePembayaranController::class, 'ambilMetodeUntukAV']);
});

// Admin Venue | Kelola Booking
Route::middleware(['auth:sanctum', CheckAdminVenue::class])
    ->prefix('booking')
    ->controller(BookingController::class)
    ->group(function () {
        Route::get('/venue/{venueId}/booking',  'ambilBookingByVenue');
        Route::get('/venue/{venueId}/booking/{bookingId}',  'ambilBookingByVenueAndId');
        Route::patch('/venue/{venueId}/booking/{bookingId}/status',  'updateStatusBooking');
    });





// ---------------------------------------------------------------------------------------------------------------------
// Venue Favorit | Infobar
Route::middleware(['auth:sanctum', CheckInfobar::class])
    ->prefix('favorit')
    ->controller(VenueController::class)
    ->group(function () {
        Route::post('/venue/{venueId}', 'tambahFavorit');  // Tambah venue ke favorit
        Route::delete('/venue/{venueId}', 'hapusFavorit'); // Hapus venue dari favorit
        Route::get('/', 'ambilFavorit');
    });

// Booking | Infobar
Route::middleware(['auth:sanctum'])
    ->prefix('booking')
    ->controller(BookingController::class)
    ->group(function () {
        Route::post('/', 'buatBooking');
        Route::get('/user',  'ambilBookingUser');
        Route::patch('/{id}/confirm', 'konfirmasiBooking');
        Route::patch('/{id}/cancel',  'batalkanBooking');
    });




// Pertandingan | Umum
Route::prefix('konten')->controller(PertandinganController::class)->group(function () {
    Route::get('/aktif', 'ambilSemuaPertandinganAktif');
    Route::get('/{id}', 'ambilDetailPertandingan');
});

// Olahraga - Umum
Route::prefix('sports')->controller(SportsController::class)->group(function () {
    Route::get('/categories', 'ambilKategori');
    Route::get('/{sport}/countries', 'ambilNegaraBerdasarkanKategori');
    Route::get('/{sport}/leagues', 'ambilLigaBerdasarkanKategoriNegaraMusim');
    Route::get('/{sport}/teams', 'ambilTimBerdasarkanLiga');
    Route::post('/schedule', 'buatJadwal');
    Route::get('/{sport}/fixtures', 'ambilPertandinganBerdasarkanMusim');
});

// Venue - Umum
Route::prefix('venue')->controller(VenueController::class)->group(function () {
    Route::get('/aktif', 'ambilSemuaVenueAktif');
    Route::get('/pertandingan/{pertandinganId}/aktif', 'ambilVenueAktifBerdasarkanPertandinganAktif');
    Route::get('/kota/{city}', 'ambilVenueBerdasarkanKota');
    Route::get('/{id}', 'detailVenue');
});

Route::prefix('menus')->controller(MenuController::class)->group(function () {
    Route::get('/venue/{venueId}/tersedia', 'menuAktifBerdasarkanVenue'); // Bisa diakses tanpa login
});