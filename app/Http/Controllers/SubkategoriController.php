<?php

namespace App\Http\Controllers;

use App\Models\Subkategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubkategoriController extends Controller
{
    public function semuaSubkategori()
    {
        $subkategori = Subkategori::with('kategori')->get();

        return response()->json(['data' => $subkategori]);
    }

    public function tambahSubkategori(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama' => 'required|string|unique:subkategori,nama',
            'deskripsi' => 'nullable|string',
        ]);

        $subkategori = Subkategori::create([
            'kategori_id' => $request->kategori_id,
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json(['message' => 'Subkategori berhasil ditambahkan.', 'data' => $subkategori]);
    }

    public function rincianSubkategori($id)
    {
        $subkategori = Subkategori::with('kategori')->find($id);

        if (!$subkategori) {
            return response()->json(['message' => 'Subkategori tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $subkategori]);
    }

    public function subkategoriBerdasarkanKategori($kategoriId)
    {
        $subkategori = Subkategori::where('kategori_id', $kategoriId)->get();

        if ($subkategori->isEmpty()) {
            return response()->json(['message' => 'Tidak ada subkategori untuk kategori ini.'], 404);
        }

        return response()->json(['data' => $subkategori]);
    }

    public function ubahSubkategori(Request $request, $id)
    {
        $subkategori = Subkategori::findOrFail($id);
        if (!$subkategori) {
            return response()->json(['message' => 'Subkategori tidak ditemukan.'], 404);
        }
        $request->validate([
            'nama' => 'sometimes|string|unique:subkategori,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $subkategori->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json(['message' => 'Subkategori berhasil diperbarui.', 'data' => $subkategori]);
    }

    public function hapusSubkategori($id)
    {
        $subkategori = Subkategori::findOrFail($id);
        if (!$subkategori) {
            return response()->json(['message' => 'Subkategori tidak ditemukan.'], 404);
        }
        $subkategori->delete();

        return response()->json(['message' => 'Subkategori berhasil dihapus.']);
    }
}
