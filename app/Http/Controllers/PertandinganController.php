<?php

namespace App\Http\Controllers;

use App\Models\Pertandingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Foto harus berupa gambar
        ]);

        // Upload foto jika ada
        $fotoPath = null;
        $fotoUrl = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('pertandingan', 'public');
            $fotoUrl = asset('storage/' . $fotoPath); // URL lengkap
        }

        // Simpan data pertandingan
        $pertandingan = Pertandingan::create([
            'cabang_olahraga' => $request->cabang_olahraga,
            'liga' => $request->liga,
            'tim_tuan_rumah' => $request->tim_tuan_rumah,
            'logo_tuan_rumah' => $request->logo_tuan_rumah,
            'tim_tamu' => $request->tim_tamu,
            'logo_tamu' => $request->logo_tamu,
            'tanggal_pertandingan' => $request->tanggal_pertandingan,
            'waktu_pertandingan' => $request->waktu_pertandingan,
            'foto' => $fotoPath, // Simpan path asli di database
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pertandingan berhasil dibuat.',
            'data' => [
                'id' => $pertandingan->id,
                'cabang_olahraga' => $pertandingan->cabang_olahraga,
                'liga' => $pertandingan->liga,
                'tim_tuan_rumah' => $pertandingan->tim_tuan_rumah,
                'logo_tuan_rumah' => $pertandingan->logo_tuan_rumah,
                'tim_tamu' => $pertandingan->tim_tamu,
                'logo_tamu' => $pertandingan->logo_tamu,
                'tanggal_pertandingan' => $pertandingan->tanggal_pertandingan,
                'waktu_pertandingan' => $pertandingan->waktu_pertandingan,
                'foto' => $fotoUrl, // Kirim URL lengkap
            ],
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
            'data' => [
                'id' => $pertandingan->id,
                'cabang_olahraga' => $pertandingan->cabang_olahraga,
                'liga' => $pertandingan->liga,
                'tim_tuan_rumah' => $pertandingan->tim_tuan_rumah,
                'logo_tuan_rumah' => $pertandingan->logo_tuan_rumah,
                'tim_tamu' => $pertandingan->tim_tamu,
                'logo_tamu' => $pertandingan->logo_tamu,
                'tanggal_pertandingan' => $pertandingan->tanggal_pertandingan,
                'waktu_pertandingan' => $pertandingan->waktu_pertandingan,
                'foto' => $pertandingan->foto ? asset('storage/' . $pertandingan->foto) : null, // Pastikan URL lengkap dikirim
            ],
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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            if ($pertandingan->foto) {
                Storage::disk('public')->delete($pertandingan->foto);
            }

            $fotoPath = $request->file('foto')->store('pertandingan', 'public');
            $pertandingan->foto = $fotoPath;
        }

        // Update data pertandingan
        $pertandingan->update($request->except(['foto']));

        return response()->json([
            'success' => true,
            'message' => 'Pertandingan berhasil diperbarui.',
            'data' => [
                'id' => $pertandingan->id,
                'cabang_olahraga' => $pertandingan->cabang_olahraga,
                'liga' => $pertandingan->liga,
                'tim_tuan_rumah' => $pertandingan->tim_tuan_rumah,
                'logo_tuan_rumah' => $pertandingan->logo_tuan_rumah,
                'tim_tamu' => $pertandingan->tim_tamu,
                'logo_tamu' => $pertandingan->logo_tamu,
                'tanggal_pertandingan' => $pertandingan->tanggal_pertandingan,
                'waktu_pertandingan' => $pertandingan->waktu_pertandingan,
                'foto' => $pertandingan->foto ? asset('storage/' . $pertandingan->foto) : null,
            ],
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
