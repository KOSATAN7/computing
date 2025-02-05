<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function buatBooking(Request $request)
    {
        // Validasi input dari request
        $request->validate([
            'venue_id' => 'required|exists:venues,id', // Validasi venue_id
            'menu_pesanan' => 'required|array', // Memastikan menu_pesanan adalah array
            'menu_pesanan.*' => 'array', // Setiap elemen di dalam menu_pesanan adalah objek
            'menu_pesanan.*.menu_id' => 'required|integer|exists:menus,id', // Validasi id menu
            'menu_pesanan.*.jumlah' => 'required|integer|min:1', // Validasi jumlah pesanan per menu
            'jumlah_orang' => 'required|integer|min:1', // Validasi jumlah orang
            'total_harga' => 'required|numeric|min:0', // Validasi total harga
            'bukti_pembayaran' => 'nullable', // File bukti pembayaran boleh kosong
            'provider_id' => 'nullable|exists:provider_pembayarans,id' // Validasi provider_id (optional)
        ]);

        // Ambil data user yang sedang login
        $user = Auth::user();

        // Proses upload file bukti pembayaran jika ada
        $buktiPath = $request->file('bukti_pembayaran')
            ? $request->file('bukti_pembayaran')->storeAs('', $request->file('bukti_pembayaran')->hashName(), 'local')
            : null;

        // Membuat booking baru
        $booking = Booking::create([
            'user_id' => $user->id, // ID user yang melakukan booking
            'venue_id' => $request->venue_id, // ID venue
            'jumlah_orang' => $request->jumlah_orang, // Jumlah orang
            'bukti_pembayaran' => $buktiPath, // Path bukti pembayaran (jika ada)
            'provider_id' => $request->provider_id, // ID provider pembayaran (optional)
            'total_harga' => $request->total_harga, // Total harga booking
            'status' => 'menunggu' // Status booking (menunggu konfirmasi)
        ]);

        // Mengambil menu yang valid berdasarkan ID yang diberikan dalam request
        $validMenus = Menu::whereIn('id', array_column($request->menu_pesanan, 'menu_id'))
            ->where('venue_id', $request->venue_id) // Pastikan menu hanya dari venue yang sesuai
            ->get();

        // Menyiapkan data untuk melakukan relasi dengan tabel pivot
        $menuSyncData = [];
        foreach ($validMenus as $menu) {
            // Menambahkan data jumlah pesanan untuk setiap menu yang valid
            $menuSyncData[$menu->id] = [
                'jumlah_pesanan' => collect($request->menu_pesanan)
                    ->firstWhere('menu_id', $menu->id)['jumlah']
            ];
        }

        // Jika ada data menu yang valid, lakukan sinkronisasi dengan tabel pivot booking_menus
        if (!empty($menuSyncData)) {
            $booking->menus()->sync($menuSyncData);
        }

        // Memuat data terkait menu yang telah dipesan
        $booking->load(['menus' => function ($query) {
            $query->select('menus.id', 'menus.nama', 'menus.deskripsi', 'menus.harga', 'menus.foto', 'menus.kategori')
                ->withPivot('jumlah_pesanan'); // Termasuk jumlah pesanan di tabel pivot
        }]);

        // Mengembalikan response JSON dengan data booking yang baru dibuat
        return response()->json([
            'message' => 'Booking berhasil dibuat, menunggu konfirmasi.',
            'data' => $booking
        ], 201);
    }
    public function ambilBookingByVenue($venueId)
    {
        $user = Auth::user();
        $venueIds = $user->managedVenues()->pluck('id');


        if (!$venueIds->contains($venueId)) {
            return response()->json([
                'message' => 'Anda tidak punya akses terhadap venue ini'
            ], 403);
        }

        $bookings = Booking::with([
            'venue:id,nama',
            'user:id,username',
            'provider:id,nama',
            'menus:id,nama,deskripsi,harga,foto,kategori,kesediaan'
        ])
            ->where('venue_id', $venueId)
            ->get()
            ->makeHidden(['user_id', 'venue_id', 'provider_id', 'created_at', 'updated_at']);

        return response()->json([
            'message' => 'Data booking untuk venue ini',
            'data' => $bookings
        ], 200);
    }
    public function ambilBookingByVenueAndId($venueId, $bookingId)
    {
        $user = Auth::user();


        $venueIds = $user->managedVenues()->pluck('id');


        if (!$venueIds->contains($venueId)) {
            return response()->json([
                'message' => 'Anda tidak punya akses terhadap venue ini'
            ], 403);
        }


        $booking = Booking::with([
            'venue:id,nama',
            'user:id,username',
            'provider:id,nama',
            'menus:id,nama,deskripsi,harga,foto,kategori,kesediaan'
        ])
            ->where('venue_id', $venueId)
            ->where('id', $bookingId)
            ->first();


        if (!$booking) {
            return response()->json([
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data booking ditemukan',
            'data' => $booking->makeHidden(['user_id', 'venue_id', 'created_at', 'updated_at'])
        ], 200);
    }
    public function updateStatusBooking(Request $request, $venueId, $bookingId)
    {
        $user = Auth::user();


        $venueIds = $user->managedVenues()->pluck('id');


        if (!$venueIds->contains($venueId)) {
            return response()->json([
                'message' => 'Anda tidak punya akses terhadap venue ini'
            ], 403);
        }


        $request->validate([
            'status' => 'required|in:menunggu,berhasil,dibatalkan'
        ]);


        $booking = Booking::where('venue_id', $venueId)->where('id', $bookingId)->first();

        if (!$booking) {
            return response()->json([
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }


        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'message' => 'Status booking berhasil diperbarui',
            'data' => [
                'id' => $booking->id,
                'status' => $booking->status
            ]
        ], 200);
    }
    public function ambilBookingUser()
    {
        $user = Auth::user();


        $bookings = Booking::with([
            'venue:id,nama',
            'provider:id,nama',
            'menus:id,nama,deskripsi,harga,foto,kategori,kesediaan'
        ])
            ->where('user_id', $user->id)
            ->get()
            ->makeHidden(['user_id', 'venue_id', 'created_at', 'updated_at']);

        return response()->json([
            'message' => 'Data booking Anda ditemukan',
            'data' => $bookings
        ], 200);
    }
}
