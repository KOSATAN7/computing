<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class KategoriController extends Controller
{
    public function semuaKategori()
    {
        $kategori = Kategori::all();
        return response()->json(['data' => $kategori]);
    }

    public function tambahKategori(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:kategori,nama',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori = Kategori::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama), // Gunakan Str::slug() di sini
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json(['message' => 'Kategori berhasil ditambahkan.', 'data' => $kategori]);
    }


    public function rincianKategori($id)
    {
        $kategori = Kategori::with('subkategori')->find($id);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $kategori]);
    }

    public function ubahKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama' => 'sometimes|string|unique:kategori,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json(['message' => 'Kategori berhasil diperbarui.', 'data' => $kategori]);
    }

    public function hapusKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}
