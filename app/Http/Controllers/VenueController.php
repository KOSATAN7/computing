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

    public function createVenueWithAdmin(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            //admin
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',

            //venue
            'venue_name' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kapasitas' => 'required|integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'required|string',
            'kontak' => 'required|string',
            'status' => 'required|in:tersedia,tidak_tersedia',
        ]);


        $admin = User::create([
            'username' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'role' => 'admin_venue',
        ]);


        $venue = Venue::create([
            'admin_id' => $admin->id,
            'nama' => $validated['venue_name'],
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
    public function index()
    {
        $venues = Venue::all();

        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Data kosong, mohon buat terlebih dahulu.'
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data.',
            'code' => 200,
            'payload' => VenueResources::collection($venues)
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

    // public function store(Request $request)
    // {
    //     $venue = Venue::create($request->all());

    //     return response()->json([
    //         'message' => 'Sukses membuat data.',
    //         'code' => 201,
    //         'payload' => new VenueResources($venue)
    //     ], 201);
    // }

    public function update(Request $request, $id)
    {
        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
                'code' => 404
            ], 404);
        }

        $venue->update($request->all());

        return response()->json([
            'message' => 'Sukses mengupdate data.',
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

    public function delete($id)
    {
        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
                'code' => 404
            ], 404);
        }

        $venue->delete();

        return response()->json([
            'message' => 'Sukses menghapus data.',
            'code' => 200
        ], 200);
    }
}
