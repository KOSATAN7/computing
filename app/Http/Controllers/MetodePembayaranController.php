<?php

namespace App\Http\Controllers;

use App\Http\Resources\MetodePembayaranResources;
use Illuminate\Http\Request;
use App\Models\MetodePembayaran;

class MetodePembayaranController extends Controller
{
    public function ambilMetodePembayaran()
    {
        $metodePembayaran = MetodePembayaran::all();

        if (!$metodePembayaran) {
            return response()->json([
                'message' => 'Metode pembayaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data berhasil diambil',
            'data' => MetodePembayaranResources::collection($metodePembayaran)
        ]);
    }
    public function buatMetodePembayaran(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:metode_pembayarans,nama|max:255',
            'deskripsi' => 'nullable|string|max:3000',
            'aktif' => 'boolean'
        ]);

        $metodePembayaran = MetodePembayaran::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'aktif' => $request->aktif ?? true
        ]);



        return response()->json([
            'message' => 'Metode pembayaran berhasil dibuat',
            'data' => $metodePembayaran
        ], 201);
    }
    public function detailMetodePembayaran($id)
    {
        $metodePembayaran = MetodePembayaran::find($id);

        if (!$metodePembayaran) {
            return response()->json([
                'message' => 'Metode pembayaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data berhasil diambil',
            'data' => new MetodePembayaranResources($metodePembayaran)
        ]);
    }
    public function ubahMetodePembayaran(Request $request, $id)
    {
        $metodePembayaran = MetodePembayaran::find($id);

        if (!$metodePembayaran) {
            return response()->json([
                'message' => 'Metode pembayaran tidak ditemukan'
            ], 404);
        }

        // Validasi data
        $request->validate([
            'nama' => 'sometimes|string|max:255|unique:metode_pembayarans,nama,' . $id,
            'deskripsi' => 'nullable|string|max:3000',
            'aktif' => 'boolean'
        ]);

        $metodePembayaran->update($request->only(['nama', 'deskripsi', 'aktif']));

        return response()->json([
            'message' => 'Metode pembayaran berhasil diperbarui',
            'data' => new MetodePembayaranResources($metodePembayaran)
        ]);
    }
    public function ubahStatusMetodePembayaran($id)
    {
        $metodePembayaran = MetodePembayaran::find($id);

        if (!$metodePembayaran) {
            return response()->json([
                'message' => 'Metode pembayaran tidak ditemukan'
            ], 404);
        }

        $metodePembayaran->aktif = !$metodePembayaran->aktif;
        $metodePembayaran->save();

        return response()->json([
            'message' => 'Status metode pembayaran berhasil diperbarui',
            'data' => new MetodePembayaranResources($metodePembayaran)
        ]);
    }
    public function hapusMetodePembayaran($id)
    {
        $metodePembayaran = MetodePembayaran::find($id);

        if (!$metodePembayaran) {
            return response()->json([
                'message' => 'Metode pembayaran tidak ditemukan'
            ], 404);
        }

        $metodePembayaran->delete();

        return response()->json([
            'message' => 'Metode pembayaran berhasil dihapus'
        ]);
    }

    public function ambilMetodeUntukAV()
    {
        $metodePembayaran = MetodePembayaran::all();

        if (!$metodePembayaran) {
            return response()->json([
                'message' => 'Metode pembayaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data berhasil diambil',
            'data' => MetodePembayaranResources::collection($metodePembayaran)
        ]);
    }
}
