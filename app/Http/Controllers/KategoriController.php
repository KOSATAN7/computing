<?php

namespace App\Http\Controllers;

use App\Http\Resources\KategoriResources;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {  
        $venues = Kategori::all();
        
        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Data kosong, mohon buat terlebih dahulu.'
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data.',
            'code' => 200,
            'payload' => KategoriResources::collection($venues)
        ], 200);
    }

    public function show($id)
    {
        $venue = Kategori::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data.',
            'code' => 200,
            'payload' => new KategoriResources($venue)
        ], 200);
    }

    public function store(Request $request)
{
    try {
        // Validasi input
        $validatedData = $request->validate([
            'nama' => 'required|unique:kategoris,nama',
        ], [
            'nama.unique' => 'Nama kategori sudah ada, silakan gunakan nama yang lain.',
            'nama.required' => 'Nama kategori harus diisi.',
        ]);

        // Jika validasi lolos, buat data kategori baru
        $venue = Kategori::create($validatedData);

        return response()->json([
            'message' => 'Sukses membuat data.',
            'code' => 201,
            'payload' => new KategoriResources($venue)
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Gagal membuat data.',
            'errors' => $e->errors()
        ], 422);
    }
}
    public function update(Request $request, $id)
    {
        $venue = Kategori::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        try {
            // Validasi input dengan pengecualian untuk id saat ini
            $validatedData = $request->validate([
                'nama' => 'required|unique:kategoris,nama,' . $id,
            ], [
                'nama.unique' => 'Nama kategori sudah ada, silakan gunakan nama yang lain.',
                'nama.required' => 'Nama kategori harus diisi.',
            ]);

            // Update data setelah validasi
            $venue->update($validatedData);

            return response()->json([
                'message' => 'Sukses mengupdate data.',
                'code' => 200,
                'payload' => new KategoriResources($venue)
            ], 200);
            
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        $venue = Kategori::find($id);

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
