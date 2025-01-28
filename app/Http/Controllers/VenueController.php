<?php

namespace App\Http\Controllers;

use App\Http\Resources\VenueResources;
use App\Models\User;
use App\Models\Venue;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\FileService;




class VenueController extends Controller
{

    // Super Admin
    public function buatVenue(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',

            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kapasitas' => 'required|integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'required|string',
            'kontak' => 'required|string',
            'foto_utama' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'foto_foto.*' => 'file|mimes:jpg,jpeg,png|max:2048',
            'video' => 'nullable|file|mimes:mp4|max:10240',
        ]);


        $fotoUtamaPath = FileService::uploadFile($request->file('foto_utama'), 'venues');
        $fotoFotoPaths = FileService::uploadMultipleFiles($request->file('foto_foto', []), 'venues');


        $admin = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin_venue',
        ]);


        $venue = Venue::create([
            'admin_id' => $admin->id,
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'kapasitas' => $validated['kapasitas'],
            'fasilitas' => $validated['fasilitas'],
            'kota' => $validated['kota'],
            'kontak' => $validated['kontak'],
            'foto_utama' => $fotoUtamaPath,
            'foto_foto' => $fotoFotoPaths,
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Venue dan Admin Venue berhasil dibuat.',
            'payload' => [
                'admin' => $admin,
                'venue' => $venue,
            ],
        ]);
    }
    public function ambilSemuaVenue()
    {

        $venues = Venue::all();

        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Data venue kosong.'
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil semua data venue.',
            'data' => VenueResources::collection($venues),
        ]);
    }
    public function ubahVenue(Request $request, $id)
    {

        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data venue tidak ditemukan.',
                'code' => 404,
            ], 404);
        }


        $validatedData = $request->validate([
            'nama' => 'string|max:255',
            'alamat' => 'string',
            'kapasitas' => 'integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'string',
            'kontak' => 'string',
            'status' => 'in:aktif,tidak_aktif',
            'foto_utama' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'foto_foto.*' => 'file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto_utama')) {
            FileService::deleteFile($venue->foto_utama); // Hapus file lama
            $validatedData['foto_utama'] = FileService::uploadFile($request->file('foto_utama'), 'venues');
        }


        if ($request->hasFile('foto_foto')) {
            if (!empty($venue->foto_foto)) {
                FileService::deleteMultipleFiles($venue->foto_foto); // Hapus file lama
            }
            $validatedData['foto_foto'] = FileService::uploadMultipleFiles($request->file('foto_foto'), 'venues');
        }



        $venue->update([
            'nama' => $validatedData['nama'] ?? $venue->nama,
            'alamat' => $validatedData['alamat'] ?? $venue->alamat,
            'kapasitas' => $validatedData['kapasitas'] ?? $venue->kapasitas,
            'fasilitas' => $validatedData['fasilitas'] ?? $venue->fasilitas,
            'kota' => $validatedData['kota'] ?? $venue->kota,
            'kontak' => $validatedData['kontak'] ?? $venue->kontak,
            'status' => $validatedData['status'] ?? $venue->status,
            'foto_utama' => $validatedData['foto_utama'] ?? $venue->foto_utama,
            'foto_foto' => $validatedData['foto_foto'] ?? $venue->foto_foto,
        ]);

        return response()->json([
            'message' => 'Sukses mengupdate data venue.',
            'code' => 200,
            'payload' => $venue,
        ], 200);
    }


    public function ubahStatus(Request $request, $id)
    {
        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $venue->status = $venue->status === 'aktif' ? 'tidak_aktif' : 'aktif';
        $venue->save();

        return response()->json([
            'message' => 'Status venue berhasil diperbarui.',
            'code' => 200,
            'data' => [
                'id' => $venue->id,
                'nama' => $venue->nama,
                'status' => $venue->status,
            ],
        ], 200);
    }
    public function hapusVenue(Request $request, $id)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'super_admin') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya super_admin yang dapat menghapus venue.',
            ], 403);
        }

        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json([
                'message' => 'Data venue tidak ditemukan.',
                'code' => 404
            ], 404);
        }

        // Hapus akun admin yang terkait
        $admin = User::find($venue->admin_id);

        if ($admin) {
            $admin->delete();
        }

        // Hapus venue
        $venue->delete();

        return response()->json([
            'message' => 'Sukses menghapus data venue dan admin terkait.',
            'code' => 200
        ], 200);
    }



    // Admin Venue
    public function tambahkanPertandinganKeVenue(Request $request, $venueId)
    {
        $request->validate([
            'pertandingan_id' => 'required|exists:pertandingan,id', // Pastikan ID pertandingan valid
        ]);

        // Temukan venue berdasarkan ID
        $venue = Venue::find($venueId);

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        // Tambahkan pertandingan ke venue
        $venue->pertandingan()->attach($request->pertandingan_id);

        return response()->json([
            'message' => 'Pertandingan berhasil ditambahkan ke venue.',
            'code' => 200,
            'venue' => $venue->load('pertandingan'), // Sertakan relasi
        ]);
    }
    public function getPertandinganDariVenue($venueId)
    {
        $venue = Venue::with(['pertandingan' => function ($query) {
            $query->where('status', 'aktif'); // Filter hanya pertandingan aktif
        }])
            ->where('id', $venueId)
            ->where('status', 'aktif') // Pastikan venue aktif
            ->first();

        // Cek apakah venue ditemukan
        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan atau tidak aktif.',
                'code' => 404,
            ], 404);
        }

        // Cek apakah ada pertandingan aktif terkait dengan venue
        if ($venue->pertandingan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada pertandingan aktif yang terkait dengan venue ini.',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data pertandingan dari venue.',
            'code' => 200,
            'data' => $venue->pertandingan, // Data pertandingan terkait
        ]);
    }
    public function hapusPertandinganDariVenue(Request $request, $venueId)
    {
        $request->validate([
            'pertandingan_id' => 'required|exists:pertandingan,id',
        ]);

        // Temukan venue berdasarkan ID
        $venue = Venue::find($venueId);

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        // Hapus pertandingan dari venue
        $venue->pertandingan()->detach($request->pertandingan_id);

        return response()->json([
            'message' => 'Pertandingan berhasil dihapus dari venue.',
            'code' => 200,
            'venue' => $venue->load('pertandingan'),
        ]);
    }
    public function kelolaProfileAdmin(Request $request, $venueId)
    {
        // Temukan venue berdasarkan ID dan pastikan admin yang sedang login adalah pemiliknya
        $venue = Venue::where('id', $venueId)->where('admin_id', auth()->id())->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan atau Anda tidak memiliki akses ke venue ini.',
                'code' => 403,
            ], 403);
        }

        // Validasi input
        $validatedData = $request->validate([
            // Validasi untuk data admin
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . auth()->id(),
            'password' => 'nullable|string|min:8',

            // Validasi untuk data venue
            'nama_venue' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kapasitas' => 'nullable|integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'nullable|string',
            'kontak' => 'nullable|string',
            'status' => 'nullable|in:aktif,tidak_aktif',
        ]);

        // Perbarui data admin
        $admin = User::find(auth()->id());
        $admin->update([
            'username' => $validatedData['username'] ?? $admin->username,
            'email' => $validatedData['email'] ?? $admin->email,
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $admin->password,
        ]);

        // Perbarui data venue
        $venue->update([
            'nama' => $validatedData['nama_venue'] ?? $venue->nama,
            'alamat' => $validatedData['alamat'] ?? $venue->alamat,
            'kapasitas' => $validatedData['kapasitas'] ?? $venue->kapasitas,
            'fasilitas' => $validatedData['fasilitas'] ?? $venue->fasilitas,
            'kota' => $validatedData['kota'] ?? $venue->kota,
            'kontak' => $validatedData['kontak'] ?? $venue->kontak,
            'status' => $validatedData['status'] ?? $venue->status,
        ]);

        return response()->json([
            'message' => 'Profil admin dan venue berhasil diperbarui.',
            'code' => 200,
            'data' => [
                'admin' => $admin,
                'venue' => $venue,
            ],
        ], 200);
    }




    // Semua Role && Unauth
    public function semuaVenueAktif()
    {

        $venues = Venue::where('status', 'aktif')->get();

        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Data venue kosong.'
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil semua data venue.',
            'data' => VenueResources::collection($venues),
        ]);
    }
    public function getVenueByPertandingan($pertandinganId)
    {
        $venues = Venue::whereHas('pertandingan', function ($query) use ($pertandinganId) {
            $query->where('pertandingan_id', $pertandinganId);
        })->where('status', 'aktif')->get();

        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada venue yang terkait dengan pertandingan ini atau venue tidak aktif.',
                'code' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data venue untuk pertandingan.',
            'code' => 200,
            'data' => $venues
        ], 200);
    }
    public function getVenueByCity($city)
    {
        $venues = Venue::where('kota', $city)->get();

        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada venue yang ditemukan di kota ini.',
                'code' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data venue berdasarkan kota.',
            'code' => 200,
            'data' => VenueResources::collection($venues)
        ], 200);
    }
    public function detailVenue($id)
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
}
