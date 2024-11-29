<?php

namespace App\Http\Controllers;

use App\Http\Resources\VenueResources;
use App\Models\Venue;
use App\Models\Films;
use Illuminate\Http\Request;

class VenueController extends Controller
{
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

    public function store(Request $request)
    {
        $venue = Venue::create($request->all());

        return response()->json([
            'message' => 'Sukses membuat data.',
            'code' => 201,
            'payload' => new VenueResources($venue)
        ], 201);
    }

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