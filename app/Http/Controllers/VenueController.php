<?php

namespace App\Http\Controllers;

use App\Http\Resources\VenueResources;
use App\Models\Venue;
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
            'payload' => VenueResources::collection($venue)
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
            'code' => 200
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