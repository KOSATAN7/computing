<?php

namespace App\Http\Controllers;

use App\Http\Resources\VenueResources;
use App\Models\User;
use App\Models\Venue;
use App\Models\Films;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class VenueController extends Controller
{

    // Super Admin
    public function buatVenue(Request $request)
    {

        $validated = $request->validate([
            'username_admin' => 'required|string|max:255',
            'email_admin' => 'required|email|unique:users,email',
            'password_admin' => 'required|string|min:8',

            'nama_venue' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kapasitas' => 'required|integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'required|string',
            'kontak' => 'required|string',
            'status' => 'required|in:tersedia,tidak_tersedia',
        ]);


        $admin = User::create([
            'username' => $validated['username_admin'],
            'email' => $validated['email_admin'],
            'password' => Hash::make($validated['password_admin']),
            'role' => 'admin_venue',
        ]);


        $venue = Venue::create([
            'admin_id' => $admin->id,
            'nama' => $validated['nama_venue'],
            'alamat' => $validated['alamat'],
            'kapasitas' => $validated['kapasitas'],
            'fasilitas' => $validated['fasilitas'],
            'kota' => $validated['kota'],
            'kontak' => $validated['kontak'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Venue dan Admin Venue berhasil dibuat.',
            'payload' => [
                'admin' => $admin,
                'venue' => $venue,
            ],
        ]);
    }
    public function ubahVenue(Request $request, $id)
    {
        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data venue tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $validatedData = $request->validate([
            'nama_venue' => 'string|max:255',
            'alamat' => 'string',
            'kapasitas' => 'integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'string',
            'kontak' => 'string',
            'status' => 'in:tersedia,tidak_tersedia',

            // Validasi tambahan untuk data admin
            'username_admin' => 'string|max:255',
            'email_admin' => 'email|unique:users,email,' . $venue->admin_id,
            'password_admin' => 'string|min:8|nullable',
        ]);

        // Update data venue
        $venue->update([
            'nama' => $validatedData['nama_venue'] ?? $venue->nama,
            'alamat' => $validatedData['alamat'] ?? $venue->alamat,
            'kapasitas' => $validatedData['kapasitas'] ?? $venue->kapasitas,
            'fasilitas' => $validatedData['fasilitas'] ?? $venue->fasilitas,
            'kota' => $validatedData['kota'] ?? $venue->kota,
            'kontak' => $validatedData['kontak'] ?? $venue->kontak,
            'status' => $validatedData['status'] ?? $venue->status,
        ]);

        // Update data admin terkait
        $admin = User::find($venue->admin_id);

        if ($admin) {
            $admin->update([
                'username' => $validatedData['username_admin'] ?? $admin->username,
                'email' => $validatedData['email_admin'] ?? $admin->email,
                'password' => isset($validatedData['password_admin']) ? Hash::make($validatedData['password_admin']) : $admin->password,
            ]);
        }

        return response()->json([
            'message' => 'Sukses mengupdate data venue dan admin terkait.',
            'code' => 200,
            'payload' => [
                'venue' => $venue,
                'admin' => $admin,
            ],
        ], 200);
    }

    public function getSemuaVenue()
    {

        $venues = Venue::all();

        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Data venue kosong.'
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil semua data venue.',
            'data' => VenueResources::collection($venues),
        ]);
    }
    public function hapusVenue(Request $request, $id)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'super_admin') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya super_admin yang dapat menghapus venue.',
            ], 403);
        }

        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data venue tidak ditemukan.',
                'code' => 404
            ], 404);
        }

        // Hapus akun admin yang terkait
        $admin = User::find($venue->admin_id);

        if ($admin) {
            $admin->delete();
        }

        // Hapus venue
        $venue->delete();

        return response()->json([
            'message' => 'Sukses menghapus data venue dan admin terkait.',
            'code' => 200
        ], 200);
    }
    public function show($id)
    {
        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
                'code' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data.',
            'code' => 200,
            'payload' => new VenueResources($venue)
        ], 200);
    }
    public function addFilmsToVenue(Request $request, $venueId)
    {
        $venue = Venue::find($venueId);

        if (!$venue) {
            return response()->json(['message' => 'Venue tidak ditemukan.'], 404);
        }

        $validatedData = $request->validate([
            'film_ids' => 'required|array',
            'film_ids.*' => 'exists:films,id',
        ]);

        $venue->films()->syncWithoutDetaching($validatedData['film_ids']);

        return response()->json([
            'message' => 'Film berhasil ditambahkan ke venue.',
            'venue' => $venue->load('films'),
        ], 200);
    }
    public function getVenuesByFilm($filmId)
    {
        $film = Films::find($filmId);

        if (!$film) {
            return response()->json(['message' => 'Film tidak ditemukan.'], 404);
        }

        $venues = $film->venues;

        return response()->json([
            'message' => 'Berhasil mengambil data venue berdasarkan film.',
            'venues' => $venues,
        ], 200);
    }
}
