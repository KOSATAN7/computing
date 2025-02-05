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
        // Validasi inputan
        $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'menu_pesanan' => 'required|array',
            'menu_pesanan.*' => 'exists:menus,id',
            'jumlah_orang' => 'required|integer|min:1',
            'bukti_pembayaran' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $user = Auth::user();

        // Simpan bukti pembayaran jika ada
        $buktiPath = $request->file('bukti_pembayaran')
            ? $request->file('bukti_pembayaran')->storeAs('', $request->file('bukti_pembayaran')->hashName(), 'local')
            : null;


        // Simpan data booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'venue_id' => $request->venue_id,
            'jumlah_orang' => $request->jumlah_orang,
            'bukti_pembayaran' => $buktiPath,
            'status' => 'menunggu'
        ]);

        // Pastikan menu yang dipilih benar-benar milik venue yang dipesan
        $validMenus = Menu::whereIn('id', $request->menu_pesanan)
            ->where('venue_id', $request->venue_id)
            ->pluck('id')->toArray();

        // Jika ada menu yang valid, tambahkan ke booking
        if (!empty($validMenus)) {
            $booking->menus()->sync($validMenus);
        }

        // Load menus agar masuk dalam response JSON
        $booking->load('menus');

        return response()->json([
            'message' => 'Booking berhasil dibuat, menunggu konfirmasi.',
            'data' => $booking
        ], 201);
    }

    public function ambilBookingByVenue($venueId)
    {
        $user = Auth::user();

        // Ambil semua venue yang dimiliki oleh admin yang sedang login
        $venueIds = $user->managedVenues()->pluck('id');

        // Jika admin mencoba melihat venue yang tidak dia kelola
        if (!$venueIds->contains($venueId)) {
            return response()->json([
                'message' => 'Anda tidak punya akses terhadap venue ini'
            ], 403);
        }

        // Ambil semua booking untuk venue tertentu, sertakan user (username) dan venue
        $bookings = Booking::with(['venue:id,nama', 'user:id,username', 'menus:id,nama,deskripsi,harga,foto,kategori,kesediaan'])
            ->where('venue_id', $venueId)
            ->get()
            ->makeHidden(['user_id', 'venue_id', 'created_at', 'updated_at']); // Sembunyikan kolom yang tidak diperlukan

        return response()->json([
            'message' => 'Data booking untuk venue ini',
            'data' => $bookings
        ], 200);
    }

    public function ambilBookingByVenueAndId($venueId, $bookingId)
    {
        $user = Auth::user();

        // Ambil semua venue yang dimiliki oleh admin yang sedang login
        $venueIds = $user->managedVenues()->pluck('id');

        // Jika admin mencoba melihat venue yang tidak dia kelola
        if (!$venueIds->contains($venueId)) {
            return response()->json([
                'message' => 'Anda tidak punya akses terhadap venue ini'
            ], 403);
        }

        // Ambil booking berdasarkan venue_id dan booking_id
        $booking = Booking::with(['venue:id,nama', 'user:id,username', 'menus:id,nama,deskripsi,harga,foto,kategori,kesediaan'])
            ->where('venue_id', $venueId)
            ->where('id', $bookingId)
            ->first();

        // Jika booking tidak ditemukan
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

        // Ambil semua venue yang dimiliki oleh admin yang sedang login
        $venueIds = $user->managedVenues()->pluck('id');

        // Jika admin mencoba mengubah status booking di venue yang tidak dia kelola
        if (!$venueIds->contains($venueId)) {
            return response()->json([
                'message' => 'Anda tidak punya akses terhadap venue ini'
            ], 403);
        }

        // Validasi input status
        $request->validate([
            'status' => 'required|in:menunggu,berhasil,dibatalkan'
        ]);

        // Cari booking berdasarkan venue_id dan booking_id
        $booking = Booking::where('venue_id', $venueId)->where('id', $bookingId)->first();

        if (!$booking) {
            return response()->json([
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        // Update status booking
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

        $bookings = Booking::with(['venue:id,nama', 'menus:id,nama,deskripsi,harga,foto,kategori,kesediaan'])
            ->where('user_id', $user->id)
            ->get()
            ->makeHidden(['user_id', 'venue_id', 'created_at', 'updated_at']); 

        return response()->json([
            'message' => 'Data booking Anda ditemukan',
            'data' => $bookings
        ], 200);
    }
}
