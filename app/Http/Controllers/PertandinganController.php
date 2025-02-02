<?php

namespace App\Http\Controllers;

use App\Models\Pertandingan;
use Illuminate\Http\Request;

class PertandinganController extends Controller
{

    // Superadmin
    public function ambilSemuaPertandingan()
    {
        $pertandingan = Pertandingan::all();

        return response()->json([
            'success' => true,
            'data' => $pertandingan,
        ]);
    }
    public function buatPertandingan(Request $request)
    {
        $request->validate([
            'cabang_olahraga' => 'required|string',
            'liga' => 'required|string',
            'tim_tuan_rumah' => 'required|string',
            'logo_tuan_rumah' => 'required|url',
            'tim_tamu' => 'required|string',
            'logo_tamu' => 'required|url',
            'tanggal_pertandingan' => 'required|date',
            'waktu_pertandingan' => 'required',
        ]);

        $pertandingan = Pertandingan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Pertandingan berhasil dibuat.',
            'data' => $pertandingan,
        ]);
    }
    public function ambilDetailPertandingan($id)
    {
        $pertandingan = Pertandingan::find($id);

        if (!$pertandingan) {
            return response()->json([
                'success' => false,
                'message' => 'Pertandingan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pertandingan,
        ]);
    }
    public function ubahPertandingan(Request $request, $id)
    {
        $pertandingan = Pertandingan::find($id);

        if (!$pertandingan) {
            return response()->json([
                'success' => false,
                'message' => 'Pertandingan tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'cabang_olahraga' => 'sometimes|string',
            'liga' => 'sometimes|string',
            'tim_tuan_rumah' => 'sometimes|string',
            'logo_tuan_rumah' => 'sometimes|url',
            'tim_tamu' => 'sometimes|string',
            'logo_tamu' => 'sometimes|url',
            'tanggal_pertandingan' => 'sometimes|date',
            'waktu_pertandingan' => 'sometimes',
        ]);

        $pertandingan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Pertandingan berhasil diperbarui.',
            'data' => $pertandingan,
        ]);
    }
    public function ubahStatus(Request $request, $id)
    {

        $pertandingan = Pertandingan::find($id);

        if (!$pertandingan) {
            return response()->json([
                'message' => 'Pertandingan tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $pertandingan->status = $pertandingan->status === 'aktif' ? 'tidak_aktif' : 'aktif';
        $pertandingan->save();

        return response()->json([
            'message' => 'Status pertandingan berhasil diperbarui.',
            'code' => 200,
            'data' => [
                'id' => $pertandingan->id,
                'cabang_olahraga' => $pertandingan->cabang_olahraga,
                'status' => $pertandingan->status,
            ],
        ]);
    }
    public function hapusPertandingan($id)
    {
        $pertandingan = Pertandingan::find($id);

        if (!$pertandingan) {
            return response()->json([
                'success' => false,
                'message' => 'Pertandingan tidak ditemukan.',
            ], 404);
        }

        $pertandingan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pertandingan berhasil dihapus.',
        ]);
    }


    // General Semua Role && Unauth
    public function ambilSemuaPertandinganAktif()
    {

        $pertandingan = Pertandingan::where('status', 'aktif')->get();


        if ($pertandingan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada pertandingan aktif yang ditemukan.',
                'code' => 404,
            ], 404);
        }


        return response()->json([
            'message' => 'Sukses mengambil semua data pertandingan aktif.',
            'code' => 200,
            'data' => $pertandingan,
        ]);
    }
}
