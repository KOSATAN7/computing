<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


use App\Models\User;

class AuthController extends Controller
{
    public function registerSuperAdmin(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat akun super admin
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'super_admin', // Tetapkan role super_admin
        ]);

        return response()->json([
            'message' => 'Super Admin registered successfully',
            'user' => $user,
        ], 201);
    }

    public function registerInfobar(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'infobar',
        ]);

        // Buat akun infobar
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'infobar', // Tetapkan role infobar
        ]);

        return response()->json([
            'message' => 'Infobar registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // if ($user->role !== 'infobar') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        if ($user->role === 'super_admin') {

            $token = $user->createToken('SuperAdminToken')->plainTextToken;
        } elseif ($user->role === 'infobar') {
            $token = $user->createToken('InfobarToken')->plainTextToken;
        } elseif ($user->role === 'admin_venue') {
            $token = $user->createToken('AdminVenue')->plainTextToken;
        }

        return response()->json([
            'message' => 'Anda login sebagai : ' . $user->role,
            'token' => $token,
        ]);
    }

    public function checkLogin(Request $request)
    {
        // Pastikan pengguna sudah terautentikasi
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Belum Login',
            ], 401);
        }

        return response()->json([
            'message' => 'User is authenticated',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Pastikan pengguna sudah terautentikasi
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'belum login, ngapain logout?',
            ], 401);
        }

        // Hapus semua token pengguna
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Berhasil logout terimagajih',
        ]);
    }
}