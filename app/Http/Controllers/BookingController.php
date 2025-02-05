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
            'menu_pesanan.*' => 'integer|min:1', // Pastikan setiap nilai dalam array adalah jumlah pesanan
            'jumlah_orang' => 'required|integer|min:1',
            'total_harga' => 'required|numeric|min:0',
            'bukti_pembayaran' => 'nullable',
            'provider_id' => 'nullable|exists:provider_pembayarans,id'
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
            'provider_id' => $request->provider_id,
            'total_harga' => $request->total_harga,
            'status' => 'menunggu'
        ]);

        // Ambil ID menu yang valid untuk venue ini
        $validMenus = Menu::whereIn('id', array_keys($request->menu_pesanan))
            ->where('venue_id', $request->venue_id)
            ->get();

        // Menyimpan menu ke dalam pivot table dengan jumlah pesanan
        $menuSyncData = [];
        foreach ($validMenus as $menu) {
            $menuSyncData[$menu->id] = ['jumlah_pesanan' => $request->menu_pesanan[$menu->id]];
        }

        if (!empty($menuSyncData)) {
            $booking->menus()->sync($menuSyncData);
        }

        // Load relationships agar data lengkap saat dikembalikan dalam response
        $booking->load(['menus' => function ($query) {
            $query->select('menus.id', 'menus.nama', 'menus.deskripsi', 'menus.harga', 'menus.foto', 'menus.kategori')
                ->withPivot('jumlah_pesanan');
        }]);

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
