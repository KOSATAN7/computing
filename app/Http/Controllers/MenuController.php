<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Resources\MenuResources;

class MenuController extends Controller
{
    /**
     * Tambah menu baru ke venue tertentu
     */
    public function tambahMenu(Request $request, $venueId)
    {
        $venue = Venue::findOrFail($venueId);
        $menu = $venue->menus()->create($request->all());

        return response()->json([
            'message' => 'Menu berhasil ditambahkan',
            'code' => 201,
            'data' => new MenuResources($menu)
        ], 201);
    }

    /**
     * Ambil semua menu berdasarkan venue
     */
    public function ambilMenuBerdasarkanVenue($venueId)
    {
        $menus = Menu::where('venue_id', $venueId)->get();

        return response()->json([
            'message' => 'Daftar menu berhasil diambil',
            'code' => 200,
            'data' => MenuResources::collection($menus)
        ], 200);
    }

    /**
     * Ambil menu yang hanya tersedia (aktif) di venue tertentu
     */
    public function menuAktifBerdasarkanVenue($venueId)
    {
        $menus = Menu::where('venue_id', $venueId)->where('kesediaan', true)->get();

        return response()->json([
            'message' => 'Daftar menu aktif berhasil diambil',
            'code' => 200,
            'data' => MenuResources::collection($menus)
        ], 200);
    }

    /**
     * Ambil detail menu berdasarkan ID dan venue
     */
    public function ambilDetailMenu($venueId, $menuId)
    {
        $menu = Menu::where('id', $menuId)->where('venue_id', $venueId)->firstOrFail();

        return response()->json([
            'message' => 'Detail menu berhasil diambil',
            'code' => 200,
            'data' => new MenuResources($menu)
        ], 200);
    }

    /**
     * Ubah data menu tertentu
     */
    public function ubahMenu(Request $request, $venueId, $menuId)
    {
        $menu = Menu::where('id', $menuId)->where('venue_id', $venueId)->firstOrFail();
        $menu->update($request->all());

        return response()->json([
            'message' => 'Menu berhasil diperbarui',
            'code' => 200,
            'data' => new MenuResources($menu)
        ], 200);
    }

    /**
     * Hapus menu tertentu
     */
    public function hapusMenu($venueId, $menuId)
    {
        $menu = Menu::where('id', $menuId)->where('venue_id', $venueId)->firstOrFail();
        $menu->delete();

        return response()->json([
            'message' => 'Menu berhasil dihapus',
            'code' => 200,
            'data' => null
        ], 200);
    }
}
