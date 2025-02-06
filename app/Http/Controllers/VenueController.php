<?php

namespace App\Http\Controllers;

use App\Http\Resources\VenueResources;
use App\Models\User;
use App\Models\Venue;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;




class VenueController extends Controller
{

    // Super Admin
    public function buatVenue(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',

                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
                'kapasitas' => 'required|integer',
                'fasilitas' => 'nullable|array',
                'kota' => 'required|string',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
                'kontak' => 'required|string',
                'foto_utama' => 'nullable|image',
                'foto_foto.*' => 'nullable|image',
                'video' => 'nullable|file|mimes:mp4|max:10240',
            ]);

            // Cek apakah file diterima
            if ($request->hasFile('foto_utama')) {
                $fotoUtama = $request->file('foto_utama');
                if (!$fotoUtama->isValid()) {
                    return response()->json([
                        'message' => 'Gagal upload foto utama.',
                        'error' => $fotoUtama->getErrorMessage(),
                    ], 500);
                }

                $fotoUtamaPath = FileService::uploadFile($fotoUtama, 'venues');
            } else {
                $fotoUtamaPath = null;
            }

            // Cek apakah multiple files diterima
            $fotoFotoPaths = [];
            if ($request->hasFile('foto_foto')) {
                foreach ($request->file('foto_foto') as $file) {
                    if (!$file->isValid()) {
                        return response()->json([
                            'message' => 'Gagal upload salah satu foto_foto.',
                            'error' => $file->getErrorMessage(),
                        ], 500);
                    }
                    $fotoFotoPaths[] = FileService::uploadFile($file, 'venues');
                }
            }

            // Simpan data ke database
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
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'kontak' => $validated['kontak'],
                'foto_utama' => $fotoUtamaPath,
                'foto_foto' => json_encode($fotoFotoPaths),
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Venue dan Admin Venue berhasil dibuat.',
                'payload' => [
                    'admin' => $admin,
                    'venue' => $venue,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengunggah file.',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
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
            'latitude' => 'string',
            'longitude' => 'string',
            'kontak' => 'string',
            'status' => 'in:aktif,tidak_aktif',
            'foto_utama' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'foto_foto.*' => 'file|mimes:jpg,jpeg,png|max:2048',
            'video' => 'file|mimes:mp4|max:2048',
        ]);

        if ($request->hasFile('foto_utama')) {
            FileService::deleteFile($venue->foto_utama);
            $validatedData['foto_utama'] = FileService::uploadFile($request->file('foto_utama'), 'venues');
        }


        if ($request->hasFile('foto_foto')) {
            if (!empty($venue->foto_foto)) {
                FileService::deleteMultipleFiles($venue->foto_foto);
            }
            $validatedData['foto_foto'] = FileService::uploadMultipleFiles($request->file('foto_foto'), 'venues');
        }



        $venue->update([
            'nama' => $validatedData['nama'] ?? $venue->nama,
            'alamat' => $validatedData['alamat'] ?? $venue->alamat,
            'kapasitas' => $validatedData['kapasitas'] ?? $venue->kapasitas,
            'fasilitas' => $validatedData['fasilitas'] ?? $venue->fasilitas,
            'kota' => $validatedData['kota'] ?? $venue->kota,
            'latitude' => $validatedData['latitude'] ?? $venue->latitude,
            'longitude' => $validatedData['longitude'] ?? $venue->longitude,
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
    public function tambahPertandinganKeVenue(Request $request, $venueId)
    {
        $adminId = Auth::id();

        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403,
            ], 403);
        }

        $request->validate([
            'pertandingan_id' => 'required|exists:pertandingan,id',
        ]);


        if ($venue->pertandingan()->where('pertandingan_id', $request->pertandingan_id)->exists()) {
            return response()->json([
                'message' => 'Anda sudah menambahkan pertandingan ini.',
                'code' => 409,
            ], 409);
        }


        $venue->pertandingan()->attach($request->pertandingan_id);

        return response()->json([
            'message' => 'Pertandingan berhasil ditambahkan ke venue.',
            'code' => 200,
            'venue' => $venue->load('pertandingan'),
        ]);
    }
    public function ambilPertandinganDariVenue($venueId)
    {
        $venue = Venue::with('pertandingan')->where('id', $venueId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        if ($venue->pertandingan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada pertandingan yang terkait dengan venue ini.',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data pertandingan dari venue.',
            'code' => 200,
            'data' => $venue->pertandingan,
        ], 200);
    }
    public function ambilPertandinganDariVenueById($venueId, $pertandinganId)
    {
        $venue = Venue::with(['pertandingan' => function ($query) use ($pertandinganId) {
            $query->where('pertandingan.id', $pertandinganId); // ✅ Tabel dijelaskan eksplisit
        }])->where('venues.id', $venueId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        if ($venue->pertandingan->isEmpty()) {
            return response()->json([
                'message' => 'Pertandingan tidak ditemukan atau tidak terkait dengan venue ini.',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data pertandingan dari venue.',
            'code' => 200,
            'data' => $venue->pertandingan->first(),
        ], 200);
    }
    public function hapusPertandinganDariVenue(Request $request, $venueId)
    {
        $adminId = Auth::id();


        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403,
            ], 403);
        }

        $request->validate([
            'pertandingan_id' => 'required|exists:pertandingan,id',
        ]);

        $venue->pertandingan()->detach($request->pertandingan_id);

        return response()->json([
            'message' => 'Pertandingan berhasil dihapus dari venue.',
            'code' => 200,
            'venue' => $venue->load('pertandingan'),
        ]);
    }
    public function kelolaProfilAdmin(Request $request, $venueId)
    {
        $adminId = Auth::id();

        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan atau Anda tidak memiliki akses ke venue ini.',
                'code' => 403,
            ], 403);
        }

        $validatedData = $request->validate([
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $adminId,
            'password' => 'nullable|string|min:8',
            'nama_venue' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kapasitas' => 'nullable|integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'nullable|string',
            'kontak' => 'nullable|string',
        ]);

        $admin = User::find($adminId);
        $admin->update([
            'username' => $validatedData['username'] ?? $admin->username,
            'email' => $validatedData['email'] ?? $admin->email,
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $admin->password,
        ]);

        $venue->update([
            'nama' => $validatedData['nama_venue'] ?? $venue->nama,
            'alamat' => $validatedData['alamat'] ?? $venue->alamat,
            'kapasitas' => $validatedData['kapasitas'] ?? $venue->kapasitas,
            'fasilitas' => $validatedData['fasilitas'] ?? $venue->fasilitas,
            'kota' => $validatedData['kota'] ?? $venue->kota,
            'kontak' => $validatedData['kontak'] ?? $venue->kontak,
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
    public function ambilVenueBerdasarkanId($venueId)
    {
        $adminId = Auth::id();

        $venue = Venue::with('admin')->where('id', $venueId)
            ->where('admin_id', $adminId)
            ->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan atau Anda tidak memiliki akses ke venue ini.',
                'code' => 403,
            ], 403);
        }

        return response()->json([
            'message' => 'Sukses mengambil data venue.',
            'code' => 200,
            'data' => [
                'admin' => [
                    'id' => $venue->admin->id,
                    'username' => $venue->admin->username,
                    'email' => $venue->admin->email,
                ],
                'venue' => [
                    'id' => $venue->id,
                    'nama_venue' => $venue->nama,
                    'alamat' => $venue->alamat,
                    'kapasitas' => $venue->kapasitas,
                    'fasilitas' => $venue->fasilitas,
                    'kota' => $venue->kota,
                    'kontak' => $venue->kontak,
                ],
            ],
        ], 200);
    }




    // Semua Role && Unauth
    public function ambilSemuaVenueAktif()
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
    public function ambilVenueAktifBerdasarkanPertandinganAktif($pertandinganId)
    {
        // Ambil semua venue yang memiliki pertandingan tertentu & status venue = aktif & status pertandingan = aktif
        $venues = Venue::whereHas('pertandingan', function ($query) use ($pertandinganId) {
            $query->where('pertandingan.id', $pertandinganId)
                ->where('pertandingan.status', 'aktif'); // Pastikan pertandingan juga aktif
        })->where('status', 'aktif')->get();

        // Jika tidak ada venue yang ditemukan
        if ($venues->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada venue aktif yang terkait dengan pertandingan aktif ini.',
                'code' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil semua venue aktif berdasarkan pertandingan yang juga aktif.',
            'code' => 200,
            'data' => VenueResources::collection($venues)
        ], 200);
    }
    public function ambilVenueBerdasarkanKota($city)
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
    public function ambilPertandinganAktifDariVenue($venueId)
    {
        $adminId = Auth::id();


        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->with(['pertandingan' => function ($query) {
            $query->where('status', 'aktif');
        }])->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403,
            ], 403);
        }

        if ($venue->pertandingan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada pertandingan aktif yang terkait dengan venue ini.',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'message' => 'Sukses mengambil data pertandingan dari venue.',
            'code' => 200,
            'data' => $venue->pertandingan,
        ]);
    }

    public function tambahFavorit($venueId)
    {
        $user = User::user();

        if (!$user) {
            return response()->json(['message' => 'Anda harus login untuk menambahkan favorit.'], 401);
        }

        $venue = Venue::findOrFail($venueId);

        if ($user->favoriteVenues()->where('venue_id', $venueId)->exists()) {
            return response()->json(['message' => 'Venue sudah difavoritkan'], 400);
        }

        $user->favoriteVenues()->attach($venueId);

        return response()->json(['message' => 'Venue berhasil ditambahkan ke favorit'], 200);
    }




    // Hapus venue dari favorit user
    public function hapusFavorit($venueId)
    {
        $user = User::user();

        if (!$user) {
            return response()->json(['message' => 'Anda harus login untuk menghapus favorit.'], 401);
        }

        $venue = Venue::findOrFail($venueId);

        if (!$user->favoriteVenues()->where('venue_id', $venueId)->exists()) {
            return response()->json(['message' => 'Venue tidak ada dalam daftar favorit'], 404);
        }

        $user->favoriteVenues()->detach($venueId);

        return response()->json(['message' => 'Venue berhasil dihapus dari favorit'], 200);
    }




    // Ambil daftar venue yang difavoritkan oleh user tertentu
    public function ambilFavorit()
    {
        $user = User::user();

        if (!$user) {
            return response()->json(['message' => 'Anda harus login untuk melihat favorit.'], 401);
        }



        $favorites = $user->favoriteVenues()->get();

        return response()->json([
            'message' => 'Sukses mengambil daftar venue favorit.',
            'data' => $favorites,
        ]);
    }
}
