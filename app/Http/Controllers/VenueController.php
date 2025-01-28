<?php

namespace App\Http\Controllers;

use App\Http\Resources\VenueResources;
use App\Models\User;
use App\Models\Venue;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class VenueController extends Controller
{

    // Super Admin
    public function buatVenue(Request $request)
    {
        $validated = $request->validate([
            'username_admin' => 'required|string|max:255',
            'email_admin' => 'required|email|unique:users,email',
            'password_admin' => 'required|string|min:8',

            'nama_venue' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kapasitas' => 'required|integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'required|string',
            'kontak' => 'required|string',
        ]);

        // Membuat admin untuk venue
        $admin = User::create([
            'username' => $validated['username_admin'],
            'email' => $validated['email_admin'],
            'password' => Hash::make($validated['password_admin']),
            'role' => 'admin_venue',
        ]);

        // Membuat venue dengan data validasi dan status default dari database
        $venue = Venue::create([
            'admin_id' => $admin->id,
            'nama' => $validated['nama_venue'],
            'alamat' => $validated['alamat'],
            'kapasitas' => $validated['kapasitas'],
            'fasilitas' => $validated['fasilitas'],
            'kota' => $validated['kota'],
            'kontak' => $validated['kontak'],
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
            'nama_venue' => 'string|max:255',
            'alamat' => 'string',
            'kapasitas' => 'integer',
            'fasilitas' => 'nullable|array',
            'kota' => 'string',
            'kontak' => 'string',
            'status' => 'in:aktif,tidak_tersedia',

            // Validasi tambahan untuk data admin
            // 'username_admin' => 'string|max:255',
            // 'email_admin' => 'email|unique:users,email,' . $venue->admin_id,
            // 'password_admin' => 'string|min:8|nullable',
        ]);

        // Update data venue
        $venue->update([
            'nama' => $validatedData['nama_venue'] ?? $venue->nama,
            'alamat' => $validatedData['alamat'] ?? $venue->alamat,
            'kapasitas' => $validatedData['kapasitas'] ?? $venue->kapasitas,
            'fasilitas' => $validatedData['fasilitas'] ?? $venue->fasilitas,
            'kota' => $validatedData['kota'] ?? $venue->kota,
            'kontak' => $validatedData['kontak'] ?? $venue->kontak,
            'status' => $validatedData['status'] ?? $venue->status,
        ]);

        // Update data admin terkait
        $admin = User::find($venue->admin_id);

        if ($admin) {
            $admin->update([
                'username' => $validatedData['username_admin'] ?? $admin->username,
                'email' => $validatedData['email_admin'] ?? $admin->email,
                'password' => isset($validatedData['password_admin']) ? Hash::make($validatedData['password_admin']) : $admin->password,
            ]);
        }

        return response()->json([
            'message' => 'Sukses mengupdate data venue dan admin terkait.',
            'code' => 200,
            'payload' => [
                'venue' => $venue,
                // 'admin' => $admin,
            ],
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
