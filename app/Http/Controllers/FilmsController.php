<?php

namespace App\Http\Controllers;

use App\Models\Films;
use App\Http\Resources\FilmsResources;
use App\Models\Kategori;
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
            'code' => 200 ,
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

    public function showByKategori($id_kategori)
{
    // Cari kategori berdasarkan ID
    $kategori = Kategori::find($id_kategori);

    if (!$kategori) {
        return response()->json([
            'message' => 'Kategori tidak ditemukan.'
        ], 404);
    }

    // Ambil film berdasarkan kategori
    $films = Films::where('kategori', $id_kategori)->with('kategori')->get();

    if ($films->isEmpty()) {
        return response()->json([
            'message' => 'Tidak ada film yang ditemukan untuk kategori ini.'
        ], 404);
    }

    // Return response menggunakan resource
    return response()->json([
        'message' => 'Sukses mengambil data film berdasarkan kategori.',
        'kategori' => $kategori->nama, // Menampilkan nama kategori
        'payload' => FilmsResources::collection($films)
    ], 200);
}

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|exists:kategoris,id', // Validasi kategori harus ada di tabel kategoris
            'jadwal' => 'required|integer',
            'harga' => 'required|string|max:255',
            'status' => 'required|in:comingsoon,ongoing,outdate',
        ]);

        // Buat film
        $film = Films::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori, // ID kategori
            'jadwal' => $request->jadwal,
            'harga' => $request->harga,
            'status' => $request->status,
        ]);

        // Return response
        return response()->json([
            'message' => 'Film berhasil dibuat.',
            'data' => $film
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
