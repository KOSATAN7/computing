<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Resources\MenuResources;
use App\Http\Resources\MenuAktifResources;
use Illuminate\Support\Facades\Auth;


class MenuController extends Controller
{
    public function tambahMenu(Request $request, $venueId)
    {
        $adminId = Auth::id();
        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403
            ], 403);
        }

        // Validasi input termasuk gambar menu
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'kategori' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Proses upload gambar menu jika ada
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('menus', 'public'); // Simpan di storage/public/menus
        }

        // Simpan menu ke database
        $menu = $venue->menus()->create([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'harga' => $validated['harga'],
            'kategori' => $validated['kategori'],
            'foto' => $fotoPath,
        ]);

        return response()->json([
            'message' => 'Menu berhasil ditambahkan',
            'code' => 201,
            'data' => [
                'id' => $menu->id,
                'nama' => $menu->nama,
                'deskripsi' => $menu->deskripsi,
                'harga' => $menu->harga,
                'kategori' => $menu->kategori,
                'foto' => $fotoPath ? asset('storage/' . $fotoPath) : null, // Kirim URL jika ada gambar
            ]
        ], 201);
    }
    public function ambilMenuBerdasarkanVenue($venueId)
    {
        $adminId = Auth::id();
        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403
            ], 403);
        }

        $menus = Menu::where('venue_id', $venueId)->get();

        return response()->json([
            'message' => 'Daftar menu berhasil diambil',
            'code' => 200,
            'data' => MenuResources::collection($menus)
        ], 200);
    }
    public function ambilDetailMenu($venueId, $menuId)
    {
        $adminId = Auth::id();
        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403
            ], 403);
        }

        $menu = Menu::where('id', $menuId)->where('venue_id', $venueId)->firstOrFail();

        return response()->json([
            'message' => 'Detail menu berhasil diambil',
            'code' => 200,
            'data' => new MenuResources($menu)
        ], 200);
    }
    public function ubahMenu(Request $request, $venueId, $menuId)
    {
        $adminId = Auth::id();
        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403
            ], 403);
        }

        $menu = Menu::where('id', $menuId)->where('venue_id', $venueId)->firstOrFail();
        $menu->update($request->all());

        return response()->json([
            'message' => 'Menu berhasil diperbarui',
            'code' => 200,
            'data' => new MenuResources($menu)
        ], 200);
    }
    public function ubahStatusMenu($venueId, $menuId)
    {
        $adminId = Auth::id();
        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403
            ], 403);
        }

        $menu = Menu::where('id', $menuId)->where('venue_id', $venueId)->firstOrFail();

        $menu->kesediaan = !$menu->kesediaan;
        $menu->save();

        return response()->json([
            'message' => 'Status menu berhasil diperbarui',
            'code' => 200,
            'data' => [
                'id' => $menu->id,
                'nama' => $menu->nama,
                'kesediaan' => $menu->kesediaan ? 'tersedia' : 'tidak_tersedia'
            ]
        ], 200);
    }
    public function hapusMenu($venueId, $menuId)
    {
        $adminId = Auth::id();
        $venue = Venue::where('id', $venueId)->where('admin_id', $adminId)->first();

        if (!$venue) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke venue ini.',
                'code' => 403
            ], 403);
        }

        $menu = Menu::where('id', $menuId)->where('venue_id', $venueId)->firstOrFail();
        $menu->delete();

        return response()->json([
            'message' => 'Menu berhasil dihapus',
            'code' => 200,
            'data' => null
        ], 200);
    }

    // Infobar
    public function menuAktifBerdasarkanVenue($venueId)
    {
        $venue = Venue::where('id', $venueId);

        if (!$venue) {
            return response()->json([
                'message' => 'Venue tidak ditemukan atau tidak aktif.',
                'code' => 404
            ], 404);
        }
        $menus = Menu::where('venue_id', $venueId)->where('kesediaan', true)->get();

        if ($menus->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada menu aktif di venue ini.',
                'code' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Daftar menu aktif berhasil diambil.',
            'code' => 200,
            'data' => MenuAktifResources::collection($menus),
        ], 200);
    }
}
