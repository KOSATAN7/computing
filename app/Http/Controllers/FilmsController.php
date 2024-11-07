<?php

namespace App\Http\Controllers;

use App\Models\Films;
use App\Http\Resources\FilmsResources;
use Illuminate\Http\Request;

class FilmsController extends Controller
{
    public function index()
    {  
        $venues = Films::all();
        
        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Data kosong, mohon buat terlebih dahulu.'
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data.',
            'data' => $venues,
            'payload' => FilmsResources::collection($venues)
        ], 200);
    }

    public function show($id)
    {
        $venue = Films::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data.',
            'code' => 200,
            'payload' => new FilmsResources($venue)
        ], 200);
    }

    public function store(Request $request)
    {
        $venue = Films::create($request->all());

        return response()->json([
            'message' => 'Sukses membuat data.',
            'code' => 201,
            'payload' => new FilmsResources($venue)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $venue = Films::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $venue->update($request->all());

        return response()->json([
            'message' => 'Sukses mengupdate data.',
            'code' => 200,
            'payload' => new FilmsResources($venue)
        ], 200);
    }

    public function delete($id)
    {
        $venue = Films::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $venue->delete();

        return response()->json([
            'message' => 'Sukses menghapus data.',
            'code' => 200,
        ], 200);
    }
}
